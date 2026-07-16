<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Тарифы</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; padding: 2rem 1rem;">

    <div class="text-center mb-10">
        <i class="fas fa-dumbbell text-4xl mb-3" style="color: #f97316;"></i>
        <h1 class="text-white text-3xl font-bold">FitTrack</h1>
        <p class="text-gray-400 mt-2">Выберите подходящий тариф</p>
    </div>

    <div class="max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Monthly --}}
        <div class="bg-white rounded-2xl p-7 shadow-xl flex flex-col">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Ежемесячный</h2>
            <p class="text-gray-400 text-sm mb-5">Платите только когда нужно</p>
            <div class="mb-1">
                <span id="price_monthly" class="text-4xl font-bold text-gray-900">2 990</span>
                <span class="text-gray-400 text-lg"> ₸ / мес</span>
            </div>
            <span id="old_monthly" class="hidden text-sm line-through text-gray-400 mb-4"></span>
            <ul class="text-sm text-gray-600 space-y-2 mb-6 flex-1">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Статистика и аналитика</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>WhatsApp уведомления</li>
            </ul>
            <a id="btn_monthly" href="{{ route('subscription.checkout', 'monthly') }}"
               class="block text-center py-3 rounded-xl text-white font-semibold transition"
               style="background:#f97316;"
               onmouseover="this.style.background='#ea6c10'"
               onmouseout="this.style.background='#f97316'">
                Оформить
            </a>
        </div>

        {{-- Annual --}}
        <div class="rounded-2xl p-7 shadow-xl flex flex-col relative" style="background: linear-gradient(135deg, #f97316, #ea580c);">
            <div class="absolute top-4 right-4 bg-white text-orange-500 text-xs font-bold px-3 py-1 rounded-full">
                Выгоднее на 42%
            </div>
            <h2 class="text-xl font-bold text-white mb-1">Годовой</h2>
            <p class="text-orange-100 text-sm mb-5">Лучшая цена за год</p>
            <div class="mb-1">
                <span id="price_annual" class="text-4xl font-bold text-white">15 000</span>
                <span class="text-orange-100 text-lg"> ₸ / год</span>
            </div>
            <span id="old_annual" class="hidden text-sm line-through text-orange-200 mb-1"></span>
            <p class="text-orange-100 text-xs mb-5">Всего 1 250 ₸ в месяц</p>
            <ul class="text-sm text-orange-50 space-y-2 mb-6 flex-1">
                <li><i class="fas fa-check mr-2"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check mr-2"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check mr-2"></i>Статистика и аналитика</li>
                <li><i class="fas fa-check mr-2"></i>WhatsApp уведомления</li>
                <li><i class="fas fa-check mr-2"></i>Приоритетная поддержка</li>
            </ul>
            <a id="btn_annual" href="{{ route('subscription.checkout', 'annual') }}"
               class="block text-center py-3 rounded-xl text-orange-600 bg-white font-semibold transition hover:bg-orange-50">
                Оформить
            </a>
        </div>

    </div>

    {{-- Promo code --}}
    <div class="max-w-md mx-auto mt-8">
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

    @auth
    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white text-sm transition">
            <i class="fas fa-arrow-left mr-1"></i>Вернуться в приложение
        </a>
    </div>
    @endauth

<script>
const prices = { monthly: 2990, annual: 15000 };

function fmt(n) { return n.toLocaleString('ru-RU'); }

function applyPromo() {
    const code = document.getElementById('promoInput').value.trim();
    const msg  = document.getElementById('promoMsg');
    if (!code) return;

    fetch('/promo/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ code, plan: 'monthly' }),
    })
    .then(r => r.json())
    .then(data => {
        msg.classList.remove('hidden');
        if (data.valid) {
            msg.className = 'mt-2 text-sm text-green-400';
            msg.textContent = data.message;
            ['monthly', 'annual'].forEach(plan => {
                const final = Math.round(prices[plan] * (1 - data.discount_percent / 100));
                document.getElementById(`price_${plan}`).textContent = fmt(final);
                const oldEl = document.getElementById(`old_${plan}`);
                oldEl.textContent = fmt(prices[plan]) + ' ₸';
                oldEl.classList.remove('hidden');
                document.getElementById(`btn_${plan}`).href = `/pricing/checkout/${plan}?promo=${encodeURIComponent(code)}`;
            });
        } else {
            msg.className = 'mt-2 text-sm text-red-400';
            msg.textContent = data.message;
            ['monthly', 'annual'].forEach(plan => {
                document.getElementById(`price_${plan}`).textContent = fmt(prices[plan]);
                document.getElementById(`old_${plan}`).classList.add('hidden');
            });
            document.getElementById('btn_monthly').href = '{{ route("subscription.checkout", "monthly") }}';
            document.getElementById('btn_annual').href = '{{ route("subscription.checkout", "annual") }}';
        }
    });
}

const urlPromo = new URLSearchParams(window.location.search).get('promo');
if (urlPromo) { document.getElementById('promoInput').value = urlPromo; applyPromo(); }
</script>
</body>
</html>
