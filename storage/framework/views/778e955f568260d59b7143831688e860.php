<?php $__env->startSection('title', 'Редактирование пакета'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="<?php echo e(route('clients.show', $package->client)); ?>" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color: #0f2035;">Редактирование пакета</h1>
            <p class="text-gray-500 text-sm"><?php echo e($package->client->full_name); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 space-y-5">
        <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-600 text-sm">
            <ul class="space-y-1"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('packages.update', $package)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            
            <div class="pb-5 border-b border-gray-100 space-y-4">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-box text-orange-400 mr-2"></i>Параметры пакета</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Количество тренировок</label>
                        <input type="number" name="total_sessions" value="<?php echo e(old('total_sessions', $package->total_sessions)); ?>" min="1" max="100"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Стоимость (₸)</label>
                        <div class="relative">
                            <input type="number" name="price" value="<?php echo e(old('price', (float)$package->price)); ?>" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">₸</span>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center gap-3 bg-orange-50 border border-orange-200 rounded-lg px-4 py-3">
                    <i class="fas fa-calculator text-orange-400"></i>
                    <div>
                        <p class="text-xs text-gray-500">Стоимость одной тренировки</p>
                        <p class="font-bold text-lg whitespace-nowrap" style="color:#0f2035;" id="price-per-session">
                            <?php echo e(number_format((float)$package->price / max($package->total_sessions, 1), 0, '.', ' ')); ?> ₸
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата оплаты</label>
                        <input type="date" name="payment_date" value="<?php echo e(old('payment_date', $package->payment_date->format('Y-m-d'))); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_paid" value="1" <?php echo e($package->is_paid ? 'checked' : ''); ?>

                                class="w-4 h-4 rounded" style="accent-color: #f97316;">
                            <span class="text-sm font-medium text-gray-700">Оплачен</span>
                        </label>
                    </div>
                </div>
            </div>

            
            <?php
                $days = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
                $selectedDays = old('training_days', $package->client->training_days ?? []);
            ?>
            <div class="pt-5 space-y-3">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-calendar-week text-orange-400 mr-2"></i>Дни тренировок клиента</h2>
                <p class="text-xs text-gray-400">Изменение дней не пересоздаёт уже созданные занятия — только влияет на новые пакеты</p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="training_days[]" value="<?php echo e($value); ?>"
                            <?php echo e(in_array($value, $selectedDays) ? 'checked' : ''); ?>

                            class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 select-none transition
                            peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 hover:border-orange-400">
                            <?php echo e($label); ?>

                        </span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="pt-5 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition"
                    style="background-color:#f97316" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Сохранить
                </button>
                <a href="<?php echo e(route('clients.show', $package->client)); ?>"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<style>
input[type="checkbox"].sr-only:checked + span {
    background-color: #f97316;
    color: white;
    border-color: #f97316;
}
</style>

<script>
function recalc() {
    const price = parseFloat(document.querySelector('[name=price]').value) || 0;
    const sessions = parseInt(document.querySelector('[name=total_sessions]').value) || 1;
    const per = sessions > 0 ? Math.round(price / sessions) : 0;
    document.getElementById('price-per-session').textContent =
        per.toLocaleString('ru-RU') + ' ₸';
}
document.querySelector('[name=price]').addEventListener('input', recalc);
document.querySelector('[name=total_sessions]').addEventListener('input', recalc);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/packages/edit.blade.php ENDPATH**/ ?>