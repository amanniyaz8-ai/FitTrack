<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Вход</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #0a0f1e 0%, #0f2035 50%, #0a0f1e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-dumbbell text-5xl mb-3" style="color: #f97316;"></i>
            <h1 class="text-white text-3xl font-bold">FitTrack</h1>
            <p class="text-gray-400 text-sm mt-1">Система учёта тренировок</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Вход в систему</h2>

            <?php if($errors->any()): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-red-600 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($errors->first()); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            style="--tw-ring-color: #fb923c;"
                            placeholder="trainer@fittrack.kz">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Пароль</label>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-xs hover:underline" style="color:#f97316;">Забыли пароль?</a>
                        </div>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="••••••••">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded" style="accent-color: #f97316;">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Запомнить меня</label>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 text-white py-3 rounded-lg font-semibold transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    Войти
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-500">
                Нет аккаунта? <a href="<?php echo e(route('register')); ?>" class="font-medium hover:underline" style="color: #f97316;">Зарегистрироваться</a>
            </p>

            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-400 text-center">
                Demo: trainer@fittrack.kz / password
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/auth/login.blade.php ENDPATH**/ ?>