<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Конструктор — Сам себе тренер</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;font:16px/1.5 system-ui,-apple-system,"Segoe UI",Arial,sans-serif;
             background:var(--tg-theme-bg-color,#F1F3F6);color:var(--tg-theme-text-color,#1A1F29);padding:24px 16px;box-sizing:border-box;text-align:center}
        .box{max-width:380px;display:grid;gap:12px}
        .muted{color:var(--tg-theme-hint-color,#5B6474);font-size:14px}
        a{color:var(--tg-theme-link-color,#1E4FD8)}
    </style>
</head>
<body>
<div class="box" id="box">
    <b>Открываю конструктор…</b>
    <span class="muted">Проверяю доступ</span>
</div>
<script>
(function () {
    var tg = window.Telegram && window.Telegram.WebApp;
    var box = document.getElementById('box');
    function show(title, text) { box.innerHTML = '<b>' + title + '</b><span class="muted">' + text + '</span>'; }
    if (!tg || !tg.initData) {
        show('Открой конструктор через бота', 'Эта страница работает внутри Telegram. Или войди по <a href="{{ route('konstruktor.login') }}">ссылке для компьютера</a>.');
        return;
    }
    tg.ready(); tg.expand();
    fetch('{{ route('konstruktor.telegramAuth') }}', {
        method: 'POST', credentials: 'same-origin',
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({initData: tg.initData})
    }).then(function (r) { return r.json().then(function (j) { return {ok: r.ok, j: j}; }); })
      .then(function (res) {
          if (res.ok && res.j.redirect) { location.replace(res.j.redirect); return; }
          if (res.j.error === 'not_member') { show('Сначала вступи в закрытый канал курса', 'Ссылка-приглашение пришла от бота после оплаты. Вступил — открой конструктор ещё раз.'); return; }
          show('Не получилось войти', 'Закрой и открой конструктор ещё раз через бота.');
      })
      .catch(function () { show('Нет связи', 'Проверь интернет и открой конструктор ещё раз.'); });
})();
</script>
</body>
</html>
