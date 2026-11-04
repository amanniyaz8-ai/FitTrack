<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    // ID закрытого канала курса, вида -100XXXXXXXXXX. Бот должен быть в нём администратором.
    'channel_id' => env('TELEGRAM_CHANNEL_ID'),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    // Куда отправлять тех, кто ещё не купил курс (открытый канал).
    'public_channel_url' => env('TELEGRAM_PUBLIC_CHANNEL_URL', 'https://t.me/'),
    // Как часто перепроверять, что ученик всё ещё в закрытом канале (часы).
    'recheck_hours' => (int) env('KONSTRUKTOR_RECHECK_HOURS', 24),
    // Сколько дней живёт вход на устройстве.
    'remember_days' => (int) env('KONSTRUKTOR_REMEMBER_DAYS', 90),
];
