<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
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

            <?php if($errors->any()): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-red-600 text-sm">
                <ul class="space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('register')); ?>">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="Алексей Тренер">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="trainer@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                            placeholder="Минимум 8 символов">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Подтвердите пароль</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2">
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 text-white py-3 rounded-lg font-semibold transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    Зарегистрироваться
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-500">
                Уже есть аккаунт? <a href="<?php echo e(route('login')); ?>" class="font-medium hover:underline" style="color: #f97316;">Войти</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/auth/register.blade.php ENDPATH**/ ?>