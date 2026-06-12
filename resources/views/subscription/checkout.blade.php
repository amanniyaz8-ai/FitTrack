<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Оформление подписки</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:1rem;">

<div class="w-full max-w-md">
    <div class="text-center mb-6">
        <i class="fas fa-dumbbell text-4xl mb-2" style="color:#f97316;"></i>
        <h1 class="text-white text-2xl font-bold">FitTrack</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">

        {{-- Plan summary --}}
        <h2 class="text-xl font-bold text-gray-800 mb-1">Тариф: {{ $planData['label'] }}</h2>

        <div class="flex items-center gap-3 mb-6">
            @if($discountPct > 0)
                <span class="text-2xl font-bold text-gray-400 line-through">{{ number_format($planData['price'], 0, '.', ' ') }} ₸</span>
                <span class="text-3xl font-bold" style="color:#f97316;">{{ number_format($finalPrice, 0, '.', ' ') }} ₸</span>
                <span class="bg-green-100 text-green-600 text-xs font-bold px-2 py-1 rounded-full">-{{ $discountPct }}%</span>
            @else
                <span class="text-3xl font-bold text-gray-900">{{ number_format($finalPrice, 0, '.', ' ') }} ₸</span>
            @endif
        </div>

        @if($promoCode)
            <div class="flex items-center gap-2 mb-5 p-3 bg-green-50 rounded-lg border border-green-200">
                <i class="fas fa-tag text-green-500"></i>
                <span class="text-sm text-green-700">Промокод <strong>{{ $promoCode->code }}</strong> применён — скидка {{ $promoCode->discount_percent }}%</span>
            </div>
        @endif

        {{-- Kaspi Pay button --}}
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-3">Оплата через Kaspi Pay:</p>
            {{-- Replace with your Kaspi QR / payment link when integrated --}}
            <a href="https://pay.kaspi.kz" target="_blank"
               class="flex items-center justify-center gap-3 w-full py-4 rounded-xl text-white font-bold text-lg transition"
               style="background:#ef4444;"
               onmouseover="this.style.background='#dc2626'"
               onmouseout="this.style.background='#ef4444'">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Kaspi.kz_logo.svg/512px-Kaspi.kz_logo.svg.png"
                     alt="Kaspi" class="h-6 brightness-0 invert">
                Оплатить {{ number_format($finalPrice, 0, '.', ' ') }} ₸
            </a>
        </div>

        {{-- Offer agreement --}}
        <div class="flex items-start gap-3 mb-6 p-4 bg-gray-50 rounded-lg">
            <input type="checkbox" id="oferta" class="mt-1 h-4 w-4 accent-orange-500">
            <label for="oferta" class="text-sm text-gray-600">
                Нажимая «Оплатить», я принимаю условия
                <a href="/oferta" target="_blank" class="text-orange-500 underline">публичной оферты</a>
                и соглашаюсь с предоставлением услуг доступа к FitTrack на выбранный период.
            </label>
        </div>

        {{-- Back --}}
        <a href="/pricing" class="block text-center text-sm text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left mr-1"></i>Изменить тариф
        </a>
    </div>
</div>

</body>
</html>
