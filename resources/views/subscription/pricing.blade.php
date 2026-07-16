<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Тариф</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; padding: 2rem 1rem; display:flex; flex-direction:column; align-items:center; justify-content:center;">

    <div class="text-center mb-10">
        <i class="fas fa-dumbbell text-4xl mb-3" style="color: #f97316;"></i>
        <h1 class="text-white text-3xl font-bold">FitTrack</h1>
        <p class="text-gray-400 mt-2">Полный доступ ко всем функциям</p>
    </div>

    <div class="w-full max-w-sm">
        <div class="rounded-2xl p-8 shadow-2xl flex flex-col items-center text-center" style="background: linear-gradient(135deg, #f97316, #ea580c);">
            <div class="bg-white bg-opacity-20 rounded-full px-4 py-1 text-white text-sm font-semibold mb-4">
                Единый тариф
            </div>
            <div class="mb-2">
                <span id="price_display" class="text-5xl font-bold text-white">5 000</span>
                <span class="text-orange-100 text-xl"> ₸ / мес</span>
            </div>
            <span id="old_price" class="hidden text-sm line-through text-orange-200 mb-1"></span>
            <p class="text-orange-100 text-sm mb-6">Отменить можно в любой момент</p>
            <ul class="text-left text-white space-y-3 mb-8 w-full">
                <li><i class="fas fa-check mr-2 opacity-80"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check mr-2 opacity-80"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check mr-2 opacity-80"></i>Статистика и аналитика</li>
                <li><i class="fas fa-check mr-2 opacity-80"></i>WhatsApp уведомления</li>
                <li><i class="fas fa-check mr-2 opacity-80"></i>Поддержка 24/7</li>
            </ul>
            <a id="buy_btn"
               href="{{ route('subscription.checkout', 'monthly') }}"
               class="w-full block text-center py-4 rounded-xl font-bold text-orange-600 bg-white transition hover:bg-orange-50 text-lg">
                Оформить подписку
            </a>
        </div>

        {{-- Promo code --}}
        <div class="mt-6">
            <p class="text-gray-400 text-sm text-center mb-3">Есть промокод?</p>
            <div class="flex gap-2">
                <input id="promoInput" type="text" placeholder="Введите промокод"
                       class="flex-1 px-4 py-3 rounded-lg border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:border-orange-500"
                       style="font-size:14px;">
                <button onclick="applyPromo()"
                        class="px-5 py-3 rounded-lg font-semibold text-white transition"
                        style="background:#f97316;"
                        onmouseover="this.style.background='#ea6c10'"
                        onmouseout="this.style.background='#f97316'">
                    Применить
                </button>
            </div>
            <p id="promoMsg" class="mt-2 text-sm hidden"></p>
        </div>
    </div>

    @auth
    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white text-sm transition">
            <i class="fas fa-arrow-left mr-1"></i>Вернуться в приложение
        </a>
    </div>
    @endauth

<script>
const originalPrice = 5000;

function fmt(n) { return n.toLocaleString('ru-RU'); }

function applyPromo() {
    const code = document.getElementById('promoInput').value.trim();
    const msg  = document.getElementById('promoMsg');
    if (!code) return;

    fetch('/promo/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ code, plan: 'monthly' }),
    })
    .then(r => r.json())
    .then(data => {
        msg.classList.remove('hidden');
        if (data.valid) {
            msg.className = 'mt-2 text-sm text-green-400';
            msg.textContent = data.message;
            const final = Math.round(originalPrice * (1 - data.discount_percent / 100));
            document.getElementById('price_display').textContent = fmt(final);
            const oldEl = document.getElementById('old_price');
            oldEl.textContent = fmt(originalPrice) + ' ₸';
            oldEl.classList.remove('hidden');
            document.getElementById('buy_btn').href = `/pricing/checkout/monthly?promo=${encodeURIComponent(code)}`;
        } else {
            msg.className = 'mt-2 text-sm text-red-400';
            msg.textContent = data.message;
            document.getElementById('price_display').textContent = fmt(originalPrice);
            document.getElementById('old_price').classList.add('hidden');
            document.getElementById('buy_btn').href = '{{ route("subscription.checkout", "monthly") }}';
        }
    });
}

const urlPromo = new URLSearchParams(window.location.search).get('promo');
if (urlPromo) {
    document.getElementById('promoInput').value = urlPromo;
    applyPromo();
}
</script>
</body>
</html>
