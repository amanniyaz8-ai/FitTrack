<div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
    <div class="h-2" style="background: linear-gradient(to right, {{ $isActive ? '#16a34a, #f97316' : '#9ca3af, #d1d5db' }});"></div>
    <div class="p-5">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-lg" style="color: #0f2035;">{{ $client->full_name }}</h3>
                    @if($isActive)
                        <span class="w-2 h-2 rounded-full bg-green-500" title="Активный"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-gray-400" title="Неактивный"></span>
                    @endif
                </div>
                @if($client->phone)
                    <p class="text-gray-500 text-sm mt-1"><i class="fas fa-phone mr-1"></i>{{ $client->phone }}</p>
                @endif
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $isActive ? '' : 'opacity-60' }}"
                  style="background-color: #e0e9ff; color: #0f2035;">
                {{ $client->packages_count }} пак.
            </span>
        </div>
        @if($client->goal)
        <p class="text-gray-600 text-sm mt-3 line-clamp-2">
            <i class="fas fa-bullseye mr-1" style="color: #fb923c;"></i>{{ $client->goal }}
        </p>
        @endif
        <div class="mt-3 flex items-center gap-2 flex-wrap">
            <p class="text-xs text-gray-400">
                <i class="fas fa-calendar-week mr-1"></i>{{ $client->training_days_label }}
            </p>
            @if($client->training_time)
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex items-center gap-1" style="background:#fff7ed;color:#f97316;">
                <i class="fas fa-clock text-xs"></i> {{ $client->training_time }}
            </span>
            @endif
            @if($client->training_type === 'mini_group')
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex items-center gap-1 bg-blue-50 text-blue-600">
                <i class="fas fa-users text-xs"></i> Мини-группа
            </span>
            @else
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex items-center gap-1 bg-green-50 text-green-600">
                <i class="fas fa-user text-xs"></i> Персональные
            </span>
            @endif
        </div>
        @if(!$isActive)
        <div class="mt-3 flex items-center gap-1 text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-1.5">
            <i class="fas fa-exclamation-circle mr-1"></i>Нет активного абонемента
        </div>
        @endif
        <div class="mt-4 flex gap-2">
            <a href="{{ route('clients.show', $client) }}"
               class="flex-1 flex items-center justify-center gap-2 text-center text-white py-2 rounded-lg text-sm transition"
               style="background-color: #0f2035;"
               onmouseover="this.style.backgroundColor='#162d4a'"
               onmouseout="this.style.backgroundColor='#0f2035'">
                <i class="fas fa-user"></i> Профиль клиента
            </a>
            @if(!$isActive)
            <a href="{{ route('packages.create', $client) }}"
               class="px-3 py-2 rounded-lg text-sm text-white transition flex items-center"
               style="background-color:#f97316;"
               onmouseover="this.style.backgroundColor='#ea580c'"
               onmouseout="this.style.backgroundColor='#f97316'"
               title="Добавить пакет">
                <i class="fas fa-plus"></i>
            </a>
            @endif
        </div>
    </div>
</div>
