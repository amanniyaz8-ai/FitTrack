<?php $__env->startSection('title', $client->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-4">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('clients.index')); ?>" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl md:text-2xl font-bold" style="color: #0f2035;"><?php echo e($client->full_name); ?></h1>
            <?php if($client->phone): ?>
                <p class="text-gray-500 text-sm"><i class="fas fa-phone mr-1"></i><?php echo e($client->phone); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="<?php echo e(route('packages.create', $client)); ?>" class="text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
            <i class="fas fa-plus"></i> Добавить пакет
        </a>
        <a href="<?php echo e(route('clients.edit', $client)); ?>" class="border border-gray-300 text-gray-500 px-3 py-2 rounded-lg text-sm hover:bg-gray-50 transition" title="Редактировать профиль клиента">
            <i class="fas fa-user-edit"></i>
        </a>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-6 mb-8">
    <!-- Info card -->
    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-3" style="color: #0f2035;"><i class="fas fa-info-circle mr-2" style="color: #fb923c;"></i>Информация</h3>
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-gray-400 block">Дни тренировок:</span>
                <p class="font-medium" style="color: #0f2035;"><?php echo e($client->training_days_label); ?></p>
            </div>
            <?php if($client->goal): ?>
            <div>
                <span class="text-gray-400 block">Цель:</span>
                <p class="text-gray-700"><?php echo e($client->goal); ?></p>
            </div>
            <?php endif; ?>
            <?php if($client->contraindications): ?>
            <div>
                <span class="text-gray-400 block">Противопоказания:</span>
                <p class="text-red-600 bg-red-50 rounded p-2"><?php echo e($client->contraindications); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="md:col-span-2 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;"><?php echo e($packages->count()); ?></p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Пакетов</p>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold text-green-600"><?php echo e($packages->sum('completed_count')); ?></p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Отходил</p>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold text-red-500"><?php echo e($packages->sum('missed_count')); ?></p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Пропустил</p>
        </div>
    </div>
</div>

<!-- Packages -->
<h2 class="text-lg font-semibold mb-4" style="color: #0f2035;">
    <i class="fas fa-box-open mr-2" style="color: #fb923c;"></i>Пакеты / Абонементы
</h2>

<?php if($packages->isEmpty()): ?>
<div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
    <i class="fas fa-box-open text-5xl mb-3"></i>
    <p>Пакетов пока нет</p>
    <a href="<?php echo e(route('packages.create', $client)); ?>" class="mt-3 inline-block text-white px-5 py-2 rounded-lg transition text-sm" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
        Добавить пакет
    </a>
</div>
<?php else: ?>
<div class="space-y-4">
    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $remaining = $pkg->total_sessions - $pkg->completed_count - $pkg->missed_count;
        $isArchived = $remaining <= 0;
        $progressPercent = $pkg->total_sessions > 0 ? (int) round(($pkg->completed_count / $pkg->total_sessions) * 100) : 0;
        $formattedPrice = number_format((float) $pkg->price, 0, '.', ' ') . ' ₸';
    ?>
    <div class="bg-white rounded-xl shadow overflow-hidden <?php echo e($isArchived ? 'opacity-70' : ''); ?>">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <?php if($isArchived): ?>
                    <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full font-medium">Архив</span>
                <?php else: ?>
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Активный</span>
                <?php endif; ?>
                <span class="font-medium" style="color: #0f2035;"><?php echo e($pkg->total_sessions); ?> тренировок</span>
                <span class="text-gray-400 text-sm"><?php echo e($formattedPrice); ?></span>
                <?php if($pkg->is_paid): ?>
                    <span class="text-green-600 text-xs"><i class="fas fa-check-circle"></i> Оплачен</span>
                <?php else: ?>
                    <span class="text-red-500 text-xs"><i class="fas fa-times-circle"></i> Не оплачен</span>
                <?php endif; ?>
                <span class="text-gray-400 text-xs"><?php echo e($pkg->payment_date->format('d.m.Y')); ?></span>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="<?php echo e(route('packages.edit', $pkg)); ?>"
                   class="text-sm border border-gray-300 text-gray-500 px-2 md:px-3 py-1.5 rounded-lg hover:bg-gray-50 hover:border-orange-400 hover:text-orange-500 transition"
                   title="Редактировать пакет">
                    <i class="fas fa-edit md:mr-1"></i><span class="hidden md:inline">Редактировать</span>
                </a>
                <a href="<?php echo e(route('packages.sessions', $pkg)); ?>" class="text-sm text-white px-3 md:px-4 py-1.5 rounded-lg transition whitespace-nowrap" style="background-color: #0f2035;" onmouseover="this.style.backgroundColor='#162d4a'" onmouseout="this.style.backgroundColor='#0f2035'">
                    Занятия
                </a>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between text-xs md:text-sm mb-2">
                <div class="flex gap-2 md:gap-4 flex-wrap">
                    <span class="text-green-600"><i class="fas fa-check mr-1"></i><?php echo e($pkg->completed_count); ?> выполнено</span>
                    <span class="text-red-500"><i class="fas fa-times mr-1"></i><?php echo e($pkg->missed_count); ?> пропущено</span>
                    <span class="text-blue-600"><i class="fas fa-clock mr-1"></i><?php echo e($remaining); ?> осталось</span>
                </div>
                <span class="text-gray-400 ml-2 shrink-0"><?php echo e($progressPercent); ?>%</span>
            </div>
            <!-- Progress bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all" style="width: <?php echo e($progressPercent); ?>%; background: linear-gradient(to right, #fb923c, #ea580c);"></div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/clients/show.blade.php ENDPATH**/ ?>