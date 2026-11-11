<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Telegram
{
    /** Вызов метода Bot API. Возвращает result или null при ошибке. */
    public function call(string $method, array $params = []): mixed
    {
        foreach ($params as $k => $v) {
            if (is_array($v)) {
                $params[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
            }
        }

        try {
            $res = Http::timeout(15)->asForm()->post('https://api.telegram.org/bot'.config('telegram.bot_token').'/'.$method, $params);
        } catch (\Throwable $e) {
            Log::warning("Telegram {$method} failed: ".$e->getMessage());
            return null;
        }

        if (! $res->ok() || ! $res->json('ok')) {
            Log::warning("Telegram {$method} error: ".$res->body());
            return null;
        }

        return $res->json('result');
    }

    /** Отправить файл, созданный на лету (программа, таблица). */
    public function sendFile(int $chatId, string $contents, string $filename, string $caption = ''): bool
    {
        try {
            $res = Http::timeout(30)->attach('document', $contents, $filename)
                ->post('https://api.telegram.org/bot'.config('telegram.bot_token').'/sendDocument', ['chat_id' => $chatId, 'caption' => $caption]);
        } catch (\Throwable $e) {
            Log::warning('Telegram sendDocument failed: '.$e->getMessage());
            return false;
        }
        if (! $res->ok() || ! $res->json('ok')) {
            Log::warning('Telegram sendDocument error: '.$res->body());
            return false;
        }

        return true;
    }

    /** true — человек состоит в закрытом канале курса. */
    public function isChannelMember(int $telegramId): bool
    {
        $r = $this->call('getChatMember', ['chat_id' => config('telegram.channel_id'), 'user_id' => $telegramId]);
        if (! $r) {
            return false;
        }
        $status = $r['status'] ?? null;

        return in_array($status, ['member', 'administrator', 'creator'], true)
            || ($status === 'restricted' && ($r['is_member'] ?? false) === true);
    }

    /** $buttons — ряды кнопок: [[['text'=>..,'url'=>..]], [['text'=>..,'callback_data'=>..]]] */
    public function sendMessage(int $chatId, string $text, array $buttons = []): mixed
    {
        $p = ['chat_id' => $chatId, 'text' => $text, 'disable_web_page_preview' => 'true'];
        if ($buttons) {
            $p['reply_markup'] = ['inline_keyboard' => $buttons];
        }

        return $this->call('sendMessage', $p);
    }

    public function sendReceipt(int $chatId, string $type, string $fileId, string $caption, array $buttons): mixed
    {
        $method = $type === 'photo' ? 'sendPhoto' : 'sendDocument';

        return $this->call($method, [
            'chat_id' => $chatId,
            $type === 'photo' ? 'photo' : 'document' => $fileId,
            'caption' => $caption,
            'reply_markup' => ['inline_keyboard' => $buttons],
        ]);
    }

    public function editCaption(int $chatId, int $messageId, string $caption): void
    {
        $this->call('editMessageCaption', ['chat_id' => $chatId, 'message_id' => $messageId, 'caption' => $caption]);
    }

    public function answerCallback(string $id, string $text = ''): void
    {
        $this->call('answerCallbackQuery', ['callback_query_id' => $id, 'text' => $text]);
    }

    /** Личное одноразовое приглашение в закрытый канал. */
    public function createPersonalInvite(string $name): ?string
    {
        $r = $this->call('createChatInviteLink', [
            'chat_id' => config('telegram.channel_id'),
            'name' => mb_substr($name, 0, 32),
            'member_limit' => 1,
            'expire_date' => now()->addHours(config('telegram.invite_hours'))->timestamp,
        ]);

        return $r['invite_link'] ?? null;
    }

    public function setWebhook(string $url, string $secret): mixed
    {
        return $this->call('setWebhook', ['url' => $url, 'secret_token' => $secret, 'allowed_updates' => ['message', 'callback_query']]);
    }

    /** Кнопка «Конструктор» в чате. С $chatId — только этому человеку (ученику), без — всем. */
    public function setMenuButton(string $appUrl, ?int $chatId = null): mixed
    {
        $p = ['menu_button' => ['type' => 'web_app', 'text' => 'Конструктор', 'web_app' => ['url' => $appUrl]]];
        if ($chatId) {
            $p['chat_id'] = $chatId;
        }
        return $this->call('setChatMenuButton', $p);
    }

    /** По умолчанию (для новых людей) — обычное меню команд, без кнопки конструктора. */
    public function setDefaultMenuButton(): mixed
    {
        return $this->call('setChatMenuButton', ['menu_button' => ['type' => 'commands']]);
    }

    /** Тексты профиля бота: экран до «Старт» и короткое описание. */
    public function setDescriptions(string $description, string $short): void
    {
        $this->call('setMyDescription', ['description' => $description]);
        $this->call('setMyShortDescription', ['short_description' => $short]);
    }

    /**
     * Проверка подписи Telegram Mini App (initData). Возвращает данные пользователя или null.
     * https://core.telegram.org/bots/webapps#validating-data-received-via-the-mini-app
     */
    public function verifyInitData(string $initData, int $maxAgeSeconds = 86400): ?array
    {
        parse_str($initData, $data);
        $hash = $data['hash'] ?? '';
        unset($data['hash']);
        if ($hash === '' || empty($data['user']) || empty($data['auth_date'])) {
            return null;
        }

        ksort($data);
        $check = implode("\n", array_map(fn ($k, $v) => "$k=$v", array_keys($data), $data));
        $secret = hash_hmac('sha256', (string) config('telegram.bot_token'), 'WebAppData', true);

        if (! hash_equals(hash_hmac('sha256', $check, $secret), $hash)) {
            return null;
        }
        if (time() - (int) $data['auth_date'] > $maxAgeSeconds) {
            return null;
        }

        $user = json_decode($data['user'], true);

        return is_array($user) && isset($user['id']) ? $user : null;
    }
}
