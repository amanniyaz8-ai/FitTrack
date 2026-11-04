<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Telegram
{
    private function url(string $method): string
    {
        return 'https://api.telegram.org/bot'.config('telegram.bot_token').'/'.$method;
    }

    /** true — человек состоит в закрытом канале курса. При ошибке API — false. */
    public function isChannelMember(int $telegramId): bool
    {
        try {
            $res = Http::timeout(10)->get($this->url('getChatMember'), [
                'chat_id' => config('telegram.channel_id'),
                'user_id' => $telegramId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram getChatMember failed: '.$e->getMessage());
            return false;
        }

        if (! $res->ok() || ! $res->json('ok')) {
            return false;
        }

        $status = $res->json('result.status');
        if (in_array($status, ['member', 'administrator', 'creator'], true)) {
            return true;
        }

        // В приватных группах участник с ограничениями всё ещё участник.
        return $status === 'restricted' && $res->json('result.is_member') === true;
    }

    public function sendMessage(int $chatId, string $text, ?array $button = null): void
    {
        $payload = ['chat_id' => $chatId, 'text' => $text, 'disable_web_page_preview' => true];
        if ($button) {
            $payload['reply_markup'] = json_encode(['inline_keyboard' => [[$button]]]);
        }

        try {
            Http::timeout(10)->post($this->url('sendMessage'), $payload);
        } catch (\Throwable $e) {
            Log::warning('Telegram sendMessage failed: '.$e->getMessage());
        }
    }

    public function setWebhook(string $url, string $secret): array
    {
        return Http::timeout(15)->post($this->url('setWebhook'), [
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => json_encode(['message']),
        ])->json() ?? [];
    }
}
