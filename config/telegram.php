<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    // ID закрытого канала курса, вида -100XXXXXXXXXX. Бот должен быть в нём администратором
    // с правом «Добавление подписчиков» (чтобы выдавать личные приглашения).
    'channel_id' => env('TELEGRAM_CHANNEL_ID'),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    // Бесплатный открытый канал для прогрева.
    'public_channel_url' => env('TELEGRAM_PUBLIC_CHANNEL_URL', 'https://t.me/'),
    // Telegram ID Амана: сюда бот присылает чеки на подтверждение.
    'admin_id' => (int) env('TELEGRAM_ADMIN_ID', 0),
    // Ссылка Kaspi Pay на оплату курса.
    'pay_url' => env('COURSE_PAY_URL', 'https://kaspi.kz/'),
    // Куда писать, если что-то не так с оплатой (например @username Амана).
    'support_contact' => env('COURSE_SUPPORT_CONTACT', 'Аману в личные сообщения'),
    // Сколько часов действует личное приглашение в закрытый канал.
    'invite_hours' => (int) env('COURSE_INVITE_HOURS', 168),
    // Как часто перепроверять, что ученик всё ещё в закрытом канале (часы).
    'recheck_hours' => (int) env('KONSTRUKTOR_RECHECK_HOURS', 24),
    // Сколько дней живёт вход на устройстве.
    'remember_days' => (int) env('KONSTRUKTOR_REMEMBER_DAYS', 90),
];
