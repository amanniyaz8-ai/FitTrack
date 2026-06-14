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

        <a href="https://wa.me/77082767310" class="whatsapp" target="_blank">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#16a34a"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Написать в WhatsApp
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
