<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Пробный период завершён</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div class="w-full max-w-md text-center">
        <i class="fas fa-dumbbell text-5xl mb-4" style="color: #f97316;"></i>
        <h1 class="text-white text-3xl font-bold mb-2">FitTrack</h1>

        <div class="bg-white rounded-2xl shadow-2xl p-8 mt-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #fff7ed;">
                <i class="fas fa-lock text-2xl" style="color: #f97316;"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-3">Пробный период завершён</h2>
            <p class="text-gray-500 text-sm mb-6">Ваш 14-дневный пробный период закончился. Оформите подписку, чтобы продолжить работу с FitTrack.</p>

            <a href="{{ route('pricing') }}"
               class="block w-full text-white py-3 rounded-lg font-semibold mb-3 transition"
               style="background-color: #f97316;"
               onmouseover="this.style.backgroundColor='#ea6c10'"
               onmouseout="this.style.backgroundColor='#f97316'">
                <i class="fas fa-crown mr-2"></i>Выбрать тариф
            </a>

            <a href="https://wa.me/77775387496" target="_blank"
               class="block w-full text-white py-3 rounded-lg font-semibold mb-3 transition"
               style="background-color: #25d366;"
               onmouseover="this.style.backgroundColor='#1da851'"
               onmouseout="this.style.backgroundColor='#25d366'">
                <i class="fab fa-whatsapp mr-2"></i>Написать в WhatsApp
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-3 rounded-lg font-semibold text-gray-500 border border-gray-200 hover:bg-gray-50 transition">
                    Выйти
                </button>
            </form>
        </div>
    </div>
</body>
</html>
