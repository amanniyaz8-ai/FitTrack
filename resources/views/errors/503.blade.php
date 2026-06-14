<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Техническое обслуживание</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f2035;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .logo-icon {
            font-size: 32px;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 800;
            color: #0f2035;
        }
        .icon-wrap {
            width: 80px;
            height: 80px;
            background: #fff7ed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0f2035;
            margin-bottom: 12px;
        }
        p {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            background: #fff7ed;
            color: #f97316;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 999px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 24px;
            background: #f97316;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover { background: #ea580c; }
        .whatsapp {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
            color: #16a34a;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }
        .whatsapp:hover { text-decoration: underline; }
        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 28px 0 0;
        }
        .footer {
            margin-top: 16px;
            font-size: 12px;
            color: #9ca3af;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse { animation: pulse 2s infinite; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <span class="logo-icon">🏋️</span>
            <span class="logo-text">FitTrack</span>
        </div>

        <div class="icon-wrap pulse">⚙️</div>

        <h1>Сервер временно недоступен</h1>
        <p>Мы проводим техническое обслуживание или сервер временно перегружен.</p>
        <p>Обычно это занимает не более <strong>1–2 минут</strong>.</p>

        <div class="badge">🔄 Скоро вернёмся</div>

        <br>
        <a href="javascript:location.reload()" class="btn">Обновить страницу</a>

        <a href="/support" class="whatsapp">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Обратиться в техподдержку
        </a>

        <hr class="divider">
        <p class="footer">Страница обновится автоматически через несколько секунд</p>
    </div>

    <script>
        // Авто-перезагрузка каждые 30 секунд
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>
