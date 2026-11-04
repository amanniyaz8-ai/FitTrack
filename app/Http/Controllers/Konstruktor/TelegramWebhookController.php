<?php

namespace App\Http\Controllers\Konstruktor;

use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use App\Models\Student;
use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(private Telegram $tg) {}

    public function __invoke(Request $request)
    {
        $secret = (string) config('telegram.webhook_secret');
        if ($secret === '' || ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        if ($cb = $request->input('callback_query')) {
            $this->onCallback($cb);
        } elseif ($msg = $request->input('message')) {
            if (data_get($msg, 'chat.type') === 'private' && data_get($msg, 'from')) {
                $this->onMessage($msg);
            }
        }

        return response()->noContent();
    }

    private function t(string $key): string
    {
        return strtr(config("bot_texts.$key"), [
            '{price}' => config('bot_texts.price'),
            '{support}' => config('telegram.support_contact'),
        ]);
    }

    private function appButton(): array
    {
        return ['text' => $this->t('btn_app'), 'web_app' => ['url' => route('konstruktor.app')]];
    }

    private function salesMenu(int $chatId): void
    {
        $this->tg->sendMessage($chatId, $this->t('start'), [
            [['text' => $this->t('btn_pay'), 'url' => config('telegram.pay_url')]],
            [['text' => $this->t('btn_paid'), 'callback_data' => 'paid']],
            [['text' => $this->t('btn_free_channel'), 'url' => config('telegram.public_channel_url')]],
        ]);
    }

    private function onMessage(array $msg): void
    {
        $from = $msg['from'];
        $chatId = (int) $msg['chat']['id'];
        $uid = (int) $from['id'];

        // Чек: фото или файл
        $receipt = null;
        if (! empty($msg['photo'])) {
            $receipt = ['photo', end($msg['photo'])['file_id']];
        } elseif (! empty($msg['document']['file_id'])) {
            $receipt = ['document', $msg['document']['file_id']];
        }

        if ($receipt) {
            $this->onReceipt($from, $chatId, $receipt[0], $receipt[1]);
            return;
        }

        // Любой текст / команда: участнику — меню конструктора, остальным — продажа
        if ($this->tg->isChannelMember($uid)) {
            $this->upsertStudent($from);
            $this->tg->sendMessage($chatId, $this->t('member_welcome'), [
                [$this->appButton()],
                [['text' => $this->t('btn_login_link'), 'callback_data' => 'login']],
            ]);
            return;
        }

        $this->salesMenu($chatId);
    }

    private function onReceipt(array $from, int $chatId, string $type, string $fileId): void
    {
        $p = CoursePurchase::where('telegram_id', $from['id'])->where('status', 'pending')->latest()->first()
            ?? new CoursePurchase(['telegram_id' => $from['id']]);
        $p->fill([
            'telegram_username' => $from['username'] ?? null,
            'first_name' => $from['first_name'] ?? null,
            'receipt_file_id' => $fileId,
            'receipt_type' => $type,
            'status' => 'pending',
        ])->save();

        $who = trim(($from['first_name'] ?? '').' '.($from['last_name'] ?? ''));
        $caption = "Новая оплата #{$p->id}\n{$who}".(isset($from['username']) ? " @{$from['username']}" : '')."\nTelegram ID: {$from['id']}";

        if (config('telegram.admin_id')) {
            $this->tg->sendReceipt(config('telegram.admin_id'), $type, $fileId, $caption, [[
                ['text' => '✅ Подтвердить', 'callback_data' => "ap:{$p->id}"],
                ['text' => '❌ Отклонить', 'callback_data' => "rj:{$p->id}"],
            ]]);
        }

        $this->tg->sendMessage($chatId, $this->t('receipt_received'));
    }

    private function onCallback(array $cb): void
    {
        $data = (string) ($cb['data'] ?? '');
        $uid = (int) data_get($cb, 'from.id');
        $chatId = (int) data_get($cb, 'message.chat.id', $uid);

        if ($data === 'paid') {
            $this->tg->answerCallback($cb['id']);
            $this->tg->sendMessage($chatId, $this->t('send_receipt'));
            return;
        }

        if ($data === 'login') {
            $this->tg->answerCallback($cb['id']);
            if (! $this->tg->isChannelMember($uid)) {
                $this->salesMenu($chatId);
                return;
            }
            $student = $this->upsertStudent($cb['from']);
            $this->tg->sendMessage($chatId, $this->t('login_link'), [[
                ['text' => $this->t('btn_app'), 'url' => route('konstruktor.enter', $student->issueLoginToken(30))],
            ]]);
            return;
        }

        if (preg_match('/^(ap|rj):(\d+)$/', $data, $m)) {
            // Подтверждать оплату может только Аман
            if (! config('telegram.admin_id') || $uid !== (int) config('telegram.admin_id')) {
                $this->tg->answerCallback($cb['id'], 'Нет доступа');
                return;
            }

            $p = CoursePurchase::find((int) $m[2]);
            if (! $p || $p->status !== 'pending') {
                $this->tg->answerCallback($cb['id'], 'Уже обработано');
                return;
            }

            $msgId = (int) data_get($cb, 'message.message_id');
            $oldCaption = (string) data_get($cb, 'message.caption', "Оплата #{$p->id}");

            if ($m[1] === 'ap') {
                $link = $this->tg->createPersonalInvite("Оплата #{$p->id}");
                if (! $link) {
                    $this->tg->answerCallback($cb['id'], 'Не смог создать приглашение — проверь, что бот админ канала');
                    return;
                }
                $p->update(['status' => 'approved', 'invite_link' => $link, 'decided_at' => now()]);
                $this->tg->sendMessage($p->telegram_id, $this->t('approved'), [
                    [['text' => $this->t('btn_join'), 'url' => $link]],
                    [$this->appButton()],
                ]);
                $this->tg->editCaption($chatId, $msgId, $oldCaption."\n\n✅ Подтверждено, доступ отправлен");
                $this->tg->answerCallback($cb['id'], 'Доступ отправлен');
            } else {
                $p->update(['status' => 'rejected', 'decided_at' => now()]);
                $this->tg->sendMessage($p->telegram_id, $this->t('rejected'));
                $this->tg->editCaption($chatId, $msgId, $oldCaption."\n\n❌ Отклонено");
                $this->tg->answerCallback($cb['id'], 'Отклонено');
            }
        }
    }

    private function upsertStudent(array $from): Student
    {
        return Student::updateOrCreate(
            ['telegram_id' => (int) $from['id']],
            ['telegram_username' => $from['username'] ?? null, 'first_name' => $from['first_name'] ?? null, 'membership_checked_at' => now()]
        );
    }
}
