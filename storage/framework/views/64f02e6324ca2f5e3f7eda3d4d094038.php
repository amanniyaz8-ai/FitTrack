<div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
    <div class="h-2" style="background: linear-gradient(to right, <?php echo e($isActive ? '#16a34a, #f97316' : '#9ca3af, #d1d5db'); ?>);"></div>
    <div class="p-5">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-lg" style="color: #0f2035;"><?php echo e($client->full_name); ?></h3>
                    <?php if($isActive): ?>
                        <span class="w-2 h-2 rounded-full bg-green-500" title="Активный"></span>
                    <?php else: ?>
                        <span class="w-2 h-2 rounded-full bg-gray-400" title="Неактивный"></span>
                    <?php endif; ?>
                </div>
                <?php if($client->phone): ?>
                    <p class="text-gray-500 text-sm mt-1"><i class="fas fa-phone mr-1"></i><?php echo e($client->phone); ?></p>
                <?php endif; ?>
            </div>
            <span class="text-xs px-2 py-1 rounded-full <?php echo e($isActive ? '' : 'opacity-60'); ?>"
                  style="background-color: #e0e9ff; color: #0f2035;">
                <?php echo e($client->packages_count); ?> пак.
            </span>
        </div>
        <?php if($client->goal): ?>
        <p class="text-gray-600 text-sm mt-3 line-clamp-2">
            <i class="fas fa-bullseye mr-1" style="color: #fb923c;"></i><?php echo e($client->goal); ?>

        </p>
        <?php endif; ?>
        <div class="mt-3">
            <p class="text-xs text-gray-400">
                <i class="fas fa-calendar-week mr-1"></i><?php echo e($client->training_days_label); ?>

            </p>
        </div>
        <?php if(!$isActive): ?>
        <div class="mt-3 flex items-center gap-1 text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-1.5">
            <i class="fas fa-exclamation-circle mr-1"></i>Нет активного абонемента
        </div>
        <?php endif; ?>
        <div class="mt-4 flex gap-2">
            <a href="<?php echo e(route('clients.show', $client)); ?>"
               class="flex-1 flex items-center justify-center gap-2 text-center text-white py-2 rounded-lg text-sm transition"
               style="background-color: #0f2035;"
               onmouseover="this.style.backgroundColor='#162d4a'"
               onmouseout="this.style.backgroundColor='#0f2035'">
                <i class="fas fa-user"></i> Профиль клиента
            </a>
            <?php if(!$isActive): ?>
            <a href="<?php echo e(route('packages.create', $client)); ?>"
               class="px-3 py-2 rounded-lg text-sm text-white transition flex items-center"
               style="background-color:#f97316;"
               onmouseover="this.style.backgroundColor='#ea580c'"
               onmouseout="this.style.backgroundColor='#f97316'"
               title="Добавить пакет">
                <i class="fas fa-plus"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /Users/amanniyaz8gmail.com/Desktop/Код/fittrack/resources/views/clients/_card.blade.php ENDPATH**/ ?>