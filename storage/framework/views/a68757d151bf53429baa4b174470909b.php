<?php $__env->startSection('title', 'Новый клиент'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="<?php echo e(route('clients.index')); ?>" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold" style="color: #0f2035;">Новый клиент</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="<?php echo e(route('clients.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php $client = null; ?>
            <?php echo $__env->make('clients._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Создать клиента
                </button>
                <a href="<?php echo e(route('clients.index')); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Отмена</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/clients/create.blade.php ENDPATH**/ ?>