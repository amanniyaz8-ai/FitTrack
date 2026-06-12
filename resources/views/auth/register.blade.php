<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Регистрация</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-dumbbell text-5xl mb-3" style="color: #f97316;"></i>
            <h1 class="text-white text-3xl font-bold">FitTrack</h1>
            <p class="text-gray-400 text-sm mt-1">Регистрация тренера</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Регистрация тренера</h2>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-red-600 text-sm">
                <ul class="space-y-1">
                    @foreach($errors->all() as $e)
                        <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="Алексей Тренер">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="trainer@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-12 focus:outline-none focus:ring-2"
                                placeholder="Минимум 8 символов">
                            <button type="button" onclick="togglePassword('password','eyeIcon1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i id="eyeIcon1" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Подтвердите пароль</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-12 focus:outline-none focus:ring-2">
                            <button type="button" onclick="togglePassword('password_confirmation','eyeIcon2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i id="eyeIcon2" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Специализация</label>
                        <select name="specialization" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2">
                            <option value="fitness" {{ old('specialization')=='fitness' ? 'selected' : '' }}>Фитнес</option>
                            <option value="running" {{ old('specialization')=='running' ? 'selected' : '' }}>Бег</option>
                            <option value="swimming" {{ old('specialization')=='swimming' ? 'selected' : '' }}>Плавание</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 text-white py-3 rounded-lg font-semibold transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    Зарегистрироваться
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-500">
                Уже есть аккаунт? <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #f97316;">Войти</a>
            </p>
            <p class="mt-2 text-center text-xs text-gray-400">
                <a href="/pricing" class="hover:underline" style="color: #f97316;">Посмотреть тарифы →</a>
            </p>
        </div>
    </div>
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
