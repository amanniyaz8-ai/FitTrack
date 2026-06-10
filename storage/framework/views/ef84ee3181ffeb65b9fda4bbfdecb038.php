<?php $__env->startSection('title', 'Клиенты'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <form method="GET" class="flex-1 max-w-md">
        <div class="relative">
            <input type="text" name="search" value="<?php echo e($search); ?>"
                placeholder="<?php echo e(app()->getLocale() === 'kk' ? 'Іздеу...' : 'Поиск по имени...'); ?>"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2" style="--tw-ring-color: #fb923c;">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </form>
    <a href="<?php echo e(route('clients.create')); ?>" class="text-white px-5 py-2 rounded-lg font-medium transition flex items-center gap-2 w-fit" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
        <?php echo e(__('app.add_client')); ?>

    </a>
</div>

<?php if($activeClients->isEmpty() && $inactiveClients->isEmpty() && $unpaidClients->isEmpty()): ?>
    <div class="text-center py-16 text-gray-400 bg-white rounded-xl shadow">
        <i class="fas fa-user-plus text-6xl mb-4"></i>
        <p class="text-lg">Клиентов пока нет</p>
        <a href="<?php echo e(route('clients.create')); ?>" class="mt-4 inline-block text-white px-6 py-2 rounded-lg transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
            Добавить первого клиента
        </a>
    </div>
<?php else: ?>


<?php if($activeClients->isNotEmpty()): ?>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
        <h2 class="text-base font-semibold" style="color:#0f2035;"><?php echo e(__('app.active_clients')); ?></h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full"><?php echo e($activeClients->count()); ?></span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php $__currentLoopData = $activeClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('clients._card', ['client' => $client, 'isActive' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<?php if($unpaidClients->isNotEmpty()): ?>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
        <h2 class="text-base font-semibold text-yellow-700"><?php echo e(__('app.unpaid_clients')); ?></h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full"><?php echo e($unpaidClients->count()); ?></span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 opacity-90">
        <?php $__currentLoopData = $unpaidClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('clients._card', ['client' => $client, 'isActive' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<?php if($inactiveClients->isNotEmpty()): ?>
<div>
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        <h2 class="text-base font-semibold text-gray-500"><?php echo e(__('app.inactive_clients')); ?></h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full"><?php echo e($inactiveClients->count()); ?></span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 opacity-75">
        <?php $__currentLoopData = $inactiveClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('clients._card', ['client' => $client, 'isActive' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/clients/index.blade.php ENDPATH**/ ?>