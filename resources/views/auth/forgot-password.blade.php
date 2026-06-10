<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Восстановление пароля</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <i class="fas fa-dumbbell text-5xl mb-3" style="color: #f97316;"></i>
            <h1 class="text-white text-3xl font-bold">FitTrack</h1>
            <p class="text-gray-400 text-sm mt-1">Восстановление пароля</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#fff7ed;">
                    <i class="fas fa-lock-open" style="color:#f97316;"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Забыли пароль?</h2>
            </div>

            <p class="text-gray-500 text-sm mb-6">
                Введите email, указанный при регистрации. Мы отправим ссылку для создания нового пароля.
            </p>

            @if(session('status'))
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg px-4 py-3 mb-5 flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-red-600 text-sm">
                    @foreach($errors->all() as $e)
                        <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 text-sm {{ $errors->has('email') ? 'border-red-400' : '' }}"
                        style="--tw-ring-color: #f97316;"
                        placeholder="trainer@example.com">
                </div>

                <button type="submit"
                    class="w-full text-white py-3 rounded-lg font-semibold transition text-sm"
                    style="background-color: #f97316;"
                    onmouseover="this.style.backgroundColor='#ea580c'"
                    onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-paper-plane mr-2"></i>Отправить ссылку
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-gray-500">
                Вспомнили пароль?
                <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #f97316;">Войти</a>
            </p>
        </div>
    </div>
</body>
</html>
