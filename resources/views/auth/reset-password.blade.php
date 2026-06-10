<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Новый пароль</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <i class="fas fa-dumbbell text-5xl mb-3" style="color: #f97316;"></i>
            <h1 class="text-white text-3xl font-bold">FitTrack</h1>
            <p class="text-gray-400 text-sm mt-1">Создание нового пароля</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#fff7ed;">
                    <i class="fas fa-key" style="color:#f97316;"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Новый пароль</h2>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-red-600 text-sm">
                    @foreach($errors->all() as $e)
                        <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                        placeholder="trainer@example.com">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
                    <div class="relative">
                        <input type="password" name="password" id="pwd" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2"
                            placeholder="Минимум 8 символов">
                        <button type="button" onclick="togglePwd('pwd',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Подтвердите пароль</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="pwd2" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2"
                            placeholder="Повторите пароль">
                        <button type="button" onclick="togglePwd('pwd2',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full text-white py-3 rounded-lg font-semibold transition text-sm"
                    style="background-color: #f97316;"
                    onmouseover="this.style.backgroundColor='#ea580c'"
                    onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Сохранить новый пароль
                </button>
            </form>
        </div>
    </div>

    <script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye text-sm' : 'fas fa-eye-slash text-sm';
    }
    </script>
</body>
</html>
