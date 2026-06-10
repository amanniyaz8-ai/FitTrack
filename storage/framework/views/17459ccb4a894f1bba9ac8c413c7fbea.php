<?php $__env->startSection('title', 'Новый пакет'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="<?php echo e(route('clients.show', $client)); ?>" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color: #0f2035;">Новый пакет</h1>
            <p class="text-gray-500 text-sm">для <?php echo e($client->full_name); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <ul class="text-red-600 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('packages.store', $client)); ?>">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Количество тренировок <span class="text-red-500">*</span></label>
                    <input type="number" name="total_sessions" value="<?php echo e(old('total_sessions', 10)); ?>" min="1" max="100"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Стоимость (₸) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="price" value="<?php echo e(old('price', 60000)); ?>" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-8 focus:outline-none focus:ring-2">
                        <span class="absolute right-3 top-2.5 text-gray-400 font-medium">₸</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата оплаты <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_paid" id="is_paid" value="1" <?php echo e(old('is_paid') ? 'checked' : ''); ?>

                        class="w-4 h-4 rounded" style="accent-color: #f97316;">
                    <label for="is_paid" class="text-sm font-medium text-gray-700">Пакет оплачен</label>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                После создания занятия будут сгенерированы автоматически на основе дней тренировок клиента:
                <strong><?php echo e($client->training_days_label); ?></strong>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Создать пакет
                </button>
                <a href="<?php echo e(route('clients.show', $client)); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Отмена</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/packages/create.blade.php ENDPATH**/ ?>