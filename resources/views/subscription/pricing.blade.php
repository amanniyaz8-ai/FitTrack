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

    {{-- Header --}}
    <div class="text-center mb-10">
        <i class="fas fa-dumbbell text-4xl mb-3" style="color: #f97316;"></i>
        <h1 class="text-white text-3xl font-bold">FitTrack</h1>
        <p class="text-gray-400 mt-2">Выберите подходящий тариф</p>
    </div>

    {{-- Plan cards --}}
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Monthly --}}
        <div class="bg-white rounded-2xl p-6 shadow-xl relative flex flex-col">
            <h2 class="text-xl font-bold text-gray-800 mb-1">1 месяц</h2>
            <p class="text-gray-500 text-sm mb-4">Попробуй без обязательств</p>
            <div class="mb-4">
                <span id="price_monthly" class="text-3xl font-bold text-gray-900">4 990</span>
                <span class="text-gray-500"> ₸</span>
                <span id="old_monthly" class="hidden ml-2 text-sm line-through text-gray-400">4 990 ₸</span>
            </div>
            <ul class="text-sm text-gray-600 space-y-2 mb-6 flex-1">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Статистика и аналитика</li>
            </ul>
            <a id="btn_monthly" href="https://wa.me/77082767310?text=Хочу%20оформить%20тариф%20FitTrack%3A%201%20месяц%20—%204%20990%20₸"
               target="_blank"
               class="block text-center py-3 rounded-lg text-white font-semibold transition"
               style="background:#f97316;"
               onmouseover="this.style.background='#ea6c10'"
               onmouseout="this.style.background='#f97316'">
                Купить
            </a>
        </div>

        {{-- Half-year (popular) --}}
        <div class="rounded-2xl p-6 shadow-xl relative flex flex-col" style="background: linear-gradient(135deg, #f97316, #ea580c);">
            <div class="absolute top-3 right-3 bg-white text-orange-500 text-xs font-bold px-2 py-1 rounded-full">
                Популярный
            </div>
            <h2 class="text-xl font-bold text-white mb-1">6 месяцев</h2>
            <p class="text-orange-100 text-sm mb-4">Экономия 20% vs месячный</p>
            <div class="mb-4">
                <span id="price_halfyear" class="text-3xl font-bold text-white">19 990</span>
                <span class="text-orange-100"> ₸</span>
                <span id="old_halfyear" class="hidden ml-2 text-sm line-through text-orange-200">19 990 ₸</span>
            </div>
            <ul class="text-sm text-orange-50 space-y-2 mb-6 flex-1">
                <li><i class="fas fa-check mr-2"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check mr-2"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check mr-2"></i>Статистика и аналитика</li>
                <li><i class="fas fa-check mr-2"></i>Приоритетная поддержка</li>
            </ul>
            <a id="btn_halfyear" href="https://wa.me/77082767310?text=Хочу%20оформить%20тариф%20FitTrack%3A%206%20месяцев%20—%2019%20990%20₸"
               target="_blank"
               class="block text-center py-3 rounded-lg text-orange-600 font-semibold bg-white transition hover:bg-orange-50">
                Купить
            </a>
        </div>

        {{-- Annual --}}
        <div class="bg-white rounded-2xl p-6 shadow-xl relative flex flex-col">
            <div class="absolute top-3 right-3 bg-green-100 text-green-600 text-xs font-bold px-2 py-1 rounded-full">
                Выгодно
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">1 год</h2>
            <p class="text-gray-500 text-sm mb-4">Максимальная экономия</p>
            <div class="mb-4">
                <span id="price_annual" class="text-3xl font-bold text-gray-900">23 990</span>
                <span class="text-gray-500"> ₸</span>
                <span id="old_annual" class="hidden ml-2 text-sm line-through text-gray-400">23 990 ₸</span>
            </div>
            <ul class="text-sm text-gray-600 space-y-2 mb-6 flex-1">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Неограниченные клиенты</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Трекинг тренировок</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Статистика и аналитика</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Приоритетная поддержка</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Ранний доступ к новым функциям</li>
            </ul>
            <a id="btn_annual" href="https://wa.me/77082767310?text=Хочу%20оформить%20тариф%20FitTrack%3A%201%20год%20—%2023%20990%20₸"
               target="_blank"
               class="block text-center py-3 rounded-lg text-white font-semibold transition"
               style="background:#f97316;"
               onmouseover="this.style.background='#ea6c10'"
               onmouseout="this.style.background='#f97316'">
                Купить
            </a>
        </div>

    </div>

    {{-- Promo code input --}}
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

    {{-- Back link --}}
    @auth
    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white text-sm transition">
            <i class="fas fa-arrow-left mr-1"></i>Вернуться в приложение
        </a>
    </div>
    @endauth

<script>
const originalPrices = { monthly: 4990, halfyear: 19990, annual: 23990 };
let currentPromo = null;

function fmt(n) { return n.toLocaleString('ru-RU'); }

function applyPromo(plan) {
    const code = document.getElementById('promoInput').value.trim();
    const msg  = document.getElementById('promoMsg');
    if (!code) return;

    // Try halfyear first (most restrictive promo applies to halfyear+annual)
    const checkPlan = plan || 'halfyear';

    fetch('/promo/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ code, plan: checkPlan }),
    })
    .then(r => r.json())
    .then(data => {
        msg.classList.remove('hidden');
        if (data.valid) {
            currentPromo = { code, discount: data.discount_percent };
            msg.className = 'mt-2 text-sm text-green-400';
            msg.textContent = data.message;
            updatePrices(data.discount_percent, code);
        } else {
            currentPromo = null;
            msg.className = 'mt-2 text-sm text-red-400';
            msg.textContent = data.message;
            updatePrices(0, null);
        }
    });
}

function updatePrices(discountPct, promoCode) {
    ['monthly', 'halfyear', 'annual'].forEach(plan => {
        const orig  = originalPrices[plan];
        const final = discountPct > 0 ? Math.round(orig * (1 - discountPct / 100)) : orig;

        document.getElementById(`price_${plan}`).textContent = fmt(final);

        const oldEl = document.getElementById(`old_${plan}`);
        if (discountPct > 0) {
            oldEl.textContent = fmt(orig) + ' ₸';
            oldEl.classList.remove('hidden');
        } else {
            oldEl.classList.add('hidden');
        }

        const btn = document.getElementById(`btn_${plan}`);
        const base = `/pricing/checkout/${plan}`;
        btn.href = promoCode ? `${base}?promo=${encodeURIComponent(promoCode)}` : base;
    });
}

// Auto-apply if promo is in URL
const urlPromo = new URLSearchParams(window.location.search).get('promo');
if (urlPromo) {
    document.getElementById('promoInput').value = urlPromo;
    applyPromo();
}
</script>
</body>
</html>
