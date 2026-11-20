<?php

namespace App\Http\Controllers\Konstruktor;

use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use App\Models\Student;
use App\Services\CoursePrice;
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
            '{price}' => CoursePrice::line(),
            '{price_short}' => CoursePrice::text(),
            '{support}' => config('telegram.support_contact'),
            '{presale_price}' => config('bot_texts.presale_price'),
            '{start_date}' => config('bot_texts.start_date'),
        ]);
    }

    private function appButton(): array
    {
        return ['text' => $this->t('btn_app'), 'web_app' => ['url' => route('konstruktor.app')]];
    }

    private function salesMenu(int $chatId): void
    {
        $this->tg->setMenuButton(route('konstruktor.app'), $chatId);
        $this->tg->sendMessage($chatId, $this->t('start'), [
            [['text' => $this->t('btn_try'), 'web_app' => ['url' => route('konstruktor.app')]]],
            [['text' => $this->t('btn_pay'), 'url' => CoursePrice::payUrl()]],
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

        // /stats — только Аману: кто купил и кто реально пользуется конструктором
        if (trim((string) ($msg['text'] ?? '')) === '/stats' && config('telegram.admin_id') && $uid === (int) config('telegram.admin_id')) {
            try {
                $text = $this->statsText();
            } catch (\Throwable $e) {
                $text = 'Статистика пока недоступна: на сервере не запущена миграция (php artisan migrate).';
            }
            $this->tg->sendMessage($chatId, $text);
            return;
        }

        // /post_channel — только Аману: бот публикует в закрытом канале пост с кнопкой конструктора и закрепляет его
        if (trim((string) ($msg['text'] ?? '')) === '/post_channel' && config('telegram.admin_id') && $uid === (int) config('telegram.admin_id')) {
            $this->postToChannel($chatId);
            return;
        }

        // Любой текст / команда: участнику — меню конструктора, остальным — продажа
        if ($this->tg->isChannelMember($uid)) {
            $this->upsertStudent($from);
            $this->tg->setMenuButton(route('konstruktor.app'), $chatId);
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
                $st = Student::firstOrCreate(['telegram_id' => (int) $p->telegram_id], ['telegram_username' => $p->telegram_username, 'first_name' => $p->first_name]);
                try {
                    if (! $st->paid_at) {
                        $st->update(['paid_at' => now()]);
                    }
                } catch (\Throwable $e) {
                }
                $this->tg->setMenuButton(route('konstruktor.app'), (int) $p->telegram_id);
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

    private function statsText(): string
    {
        $paid = CoursePurchase::where('status', 'approved')->distinct('telegram_id')->count('telegram_id');
        $pending = CoursePurchase::where('status', 'pending')->count();
        $students = Student::orderBy('first_name')->get();
        $week = now()->subDays(7);
        $d = fn ($t) => $t ? $t->format('d.m') : '—';

        $lines = [
            '📊 Статистика конструктора',
            '',
            "Купили (одобрено): $paid".($pending ? " · ждут проверки: $pending" : ''),
            'Цена сейчас: '.CoursePrice::line(),
            'Попробовали бесплатно: '.$students->whereNull('paid_at')->whereNotNull('first_open_at')->count()
                .' · оплатили после пробы: '.$students->whereNotNull('paid_at')->whereNotNull('week1_nudged_at')->count(),
            'Зашли в конструктор: '.$students->whereNotNull('first_open_at')->count(),
            'Прошли анкету: '.$students->whereNotNull('onboarded_at')->count(),
            'Скачали таблицу: '.$students->whereNotNull('downloaded_at')->count(),
            'Вносили веса за 7 дней: '.$students->filter(fn ($s) => $s->last_log_at && $s->last_log_at->gte($week))->count(),
            '',
            'По ученикам (💳 оплатил / 🆓 бесплатно · вход · анкета · таблица · веса):',
        ];
        foreach ($students as $s) {
            $name = $s->first_name ?: 'Без имени';
            if ($s->telegram_username) {
                $name .= ' (@'.$s->telegram_username.')';
            }
            $paidMark = $s->paid_at ? '💳 ' : '🆓 ';
            $lines[] = "• $paidMark$name (id {$s->telegram_id}) — ".$d($s->first_open_at).' · '.($s->onboarded_at ? '✅' : '—').' · '.($s->downloaded_at ? '✅' : '—').' · '.$d($s->last_log_at);
        }
        if ($students->isEmpty()) {
            $lines[] = 'Пока никто не заходил.';
        }

        return implode("\n", $lines);
    }

    private function postToChannel(int $chatId): void
    {
        $bot = ltrim((string) (config('telegram.bot_username') ?: 'samsebetrener_kz_bot'), '@');
        $text = "🏋️ Твой конструктор программы\n\n"
            ."Ответь на вопросы анкеты — и получишь программу на 12 недель под себя: упражнения, подходы, рекомендованный вес на каждую тренировку, калории и таблицу Excel.\n\n"
            ."Как открыть: нажми кнопку ниже → «Старт» → «Открыть конструктор».";
        $sent = $this->tg->sendMessage((int) config('telegram.channel_id'), $text, [
            [['text' => '🏋️ Открыть конструктор', 'url' => "https://t.me/{$bot}?start=konstruktor"]],
        ]);
        if (! $sent) {
            $this->tg->sendMessage($chatId, 'Не получилось опубликовать. Проверь, что бот — администратор закрытого канала с правом публиковать сообщения.');
            return;
        }
        $pinned = $this->tg->call('pinChatMessage', [
            'chat_id' => config('telegram.channel_id'), 'message_id' => $sent['message_id'] ?? 0, 'disable_notification' => 'true',
        ]);
        $this->tg->sendMessage($chatId, $pinned
            ? '✅ Пост опубликован в закрытом канале и закреплён.'
            : '✅ Пост опубликован в закрытом канале. Закрепить не получилось — закрепи его вручную (зажми пост → «Закрепить»).');
    }
}
