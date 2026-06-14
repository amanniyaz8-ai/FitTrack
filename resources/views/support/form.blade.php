<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Техническая поддержка</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background:#0f2035;">

<div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">

    {{-- Лого --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <span style="color:#f97316; font-size:28px;"><i class="fas fa-dumbbell"></i></span>
        <span class="text-2xl font-bold" style="color:#0f2035;">FitTrack</span>
    </div>

    @if(session('success'))
    <div class="text-center py-8">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#f0fdf4;">
            <i class="fas fa-check-circle text-3xl text-green-500"></i>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:#0f2035;">Обращение отправлено!</h2>
        <p class="text-gray-500 text-sm mb-6">Мы рассмотрим вашу заявку и свяжемся с вами в ближайшее время.</p>
        <a href="/" class="inline-block text-white px-6 py-2 rounded-lg text-sm font-semibold" style="background:#f97316;">
            На главную
        </a>
    </div>
    @else

    <h1 class="text-xl font-bold text-center mb-1" style="color:#0f2035;">Техническая поддержка</h1>
    <p class="text-gray-400 text-sm text-center mb-6">Опишите проблему — мы поможем</p>

    <form method="POST" action="/support" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Ваше имя <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}"
                style="--tw-ring-color:#f97316;"
                placeholder="Амангазы">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Телефон</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                placeholder="+7 777 123 4567">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                placeholder="example@mail.com">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Описание проблемы <span class="text-red-500">*</span></label>
            <textarea name="message" rows="4" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                placeholder="Опишите что произошло и когда...">{{ old('message') }}</textarea>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
            @foreach($errors->all() as $e)
                <p class="text-red-600 text-xs"><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</p>
            @endforeach
        </div>
        @endif

        <button type="submit" class="w-full text-white py-3 rounded-lg font-semibold text-sm transition"
            style="background:#f97316;"
            onmouseover="this.style.background='#ea580c'"
            onmouseout="this.style.background='#f97316'">
            <i class="fas fa-paper-plane mr-2"></i>Отправить обращение
        </button>
    </form>

    @endif

    <p class="text-center text-xs text-gray-400 mt-6">
        <a href="/" class="hover:text-gray-600 transition">← Вернуться на сайт</a>
    </p>
</div>

</body>
</html>
