<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Вход в конструктор — Сам себе тренер</title>
    <style>
        :root{--bg:#F1F3F6;--card:#fff;--fg:#1A1F29;--muted:#5B6474;--accent:#1E4FD8;--line:#D3D8E1;--bad:#B3261E;--bad-bg:#FBE6E4}
        @media (prefers-color-scheme: dark){:root{--bg:#12151B;--card:#1B2029;--fg:#E8ECF2;--muted:#9AA4B5;--accent:#5B86FF;--line:#2F3744;--bad:#FF8A80;--bad-bg:#3A1C1A}}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:var(--bg);color:var(--fg);font:16px/1.5 system-ui,-apple-system,"Segoe UI",Arial,sans-serif;padding:24px 16px}
        .card{max-width:440px;width:100%;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:28px 24px;display:grid;gap:16px}
        h1{margin:0;font-size:24px;line-height:1.2}
        p{margin:0;color:var(--muted)}
        .reason{background:var(--bad-bg);color:var(--bad);padding:10px 12px;border-radius:10px;font-weight:600;font-size:14px}
        .btn{display:block;text-align:center;text-decoration:none;background:var(--accent);color:#fff;font-weight:700;padding:14px 16px;border-radius:12px}
        ol{margin:0;padding-left:20px;color:var(--muted);display:grid;gap:6px;font-size:15px}
    </style>
</head>
<body>
<main class="card">
    <h1>Конструктор тренировок и КБЖУ</h1>
    @if ($reason)
        <p class="reason">{{ $reason }}</p>
    @endif
    <p>Конструктор доступен ученикам курса «Сам себе тренер». Вход — через нашего Telegram-бота, без паролей.</p>
    <ol>
        <li>Нажми кнопку ниже — откроется бот.</li>
        <li>Нажми «Старт». Бот проверит, что ты в закрытом канале курса.</li>
        <li>Бот пришлёт личную ссылку — открой её, и ты внутри.</li>
    </ol>
    @if (config('telegram.bot_username'))
        <a class="btn" href="https://t.me/{{ config('telegram.bot_username') }}?start=konstruktor">Войти через Telegram</a>
    @else
        <p class="reason">Бот ещё не настроен (нет TELEGRAM_BOT_USERNAME в .env).</p>
    @endif
</main>
</body>
</html>
