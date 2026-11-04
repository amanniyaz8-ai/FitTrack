<?php

namespace App\Http\Controllers\Konstruktor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, Telegram $tg)
    {
        $secret = (string) config('telegram.webhook_secret');
        if ($secret === '' || ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        $message = $request->input('message');
        $text = trim((string) data_get($message, 'text', ''));
        $from = data_get($message, 'from');
        $chatId = (int) data_get($message, 'chat.id');

        // Отвечаем только в личке с ботом и только на /start.
        if (! $from || data_get($message, 'chat.type') !== 'private' || ! str_starts_with($text, '/start')) {
            return response()->noContent();
        }

        $telegramId = (int) $from['id'];

        if (! $tg->isChannelMember($telegramId)) {
            $tg->sendMessage($chatId,
                "Конструктор тренировок доступен ученикам курса «Сам себе тренер».\n\nЧтобы получить доступ, переходи в канал — там всё про курс.",
                ['text' => 'Перейти в канал', 'url' => config('telegram.public_channel_url')]
            );
            return response()->noContent();
        }

        $student = Student::updateOrCreate(
            ['telegram_id' => $telegramId],
            [
                'telegram_username' => $from['username'] ?? null,
                'first_name' => $from['first_name'] ?? null,
                'membership_checked_at' => now(),
            ]
        );

        $token = $student->issueLoginToken(30);

        $tg->sendMessage($chatId,
            "Готово! Это твоя личная ссылка на конструктор. Она одноразовая и работает 30 минут — не пересылай её.\n\nПотом сайт запомнит тебя на этом устройстве. Нужно зайти с другого телефона — снова нажми /start.",
            ['text' => 'Открыть конструктор', 'url' => route('konstruktor.enter', $token)]
        );

        return response()->noContent();
    }
}
