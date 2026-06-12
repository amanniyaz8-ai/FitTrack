@extends('layouts.app')
@section('title', 'Мониторинг')

@push('head')
{{-- Auto-refresh every 60 seconds --}}
<meta http-equiv="refresh" content="60">
@endpush

@section('content')
{{-- Last updated indicator --}}
<div class="flex items-center justify-between mb-4">
    <h1 class="text-lg md:text-xl font-bold" style="color: #0f2035;">{{ __('app.dashboard_title') }}</h1>
</div>
<!-- Stats cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow p-3 md:p-5 border-l-4" style="border-left-color: #f97316;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">{{ __('app.client_base') }}</p>
                <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;">{{ $totalClients }}</p>
            </div>
            <div class="p-2 md:p-3 rounded-full" style="background-color: #fff7ed;">
                <i class="fas fa-users text-base md:text-xl" style="color: #f97316;"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-3 md:p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">{{ __('app.active_packages') }}</p>
                <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;">{{ $activePackages }}</p>
            </div>
            <div class="bg-blue-100 p-2 md:p-3 rounded-full">
                <i class="fas fa-box text-blue-500 text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-3 md:p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">{{ __('app.sessions_today') }}</p>
                <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;">{{ $todaySessions }}</p>
            </div>
            <div class="bg-green-100 p-2 md:p-3 rounded-full">
                <i class="fas fa-calendar-check text-green-500 text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-3 md:p-5 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">{{ app()->getLocale() === 'kk' ? 'Аяқталуда' : 'Заканчиваются' }}</p>
                <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;">{{ $expiringPackages->count() }}</p>
            </div>
            <div class="bg-yellow-100 p-2 md:p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-base md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Warning: expiring packages -->
@if($expiringPackages->count() > 0)
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
    <h3 class="font-semibold text-yellow-800 mb-2">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('app.expiring_packages') }}
    </h3>
    <div class="space-y-2">
        @foreach($expiringPackages as $pkg)
        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 shadow-sm">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <a href="{{ route('clients.show', $pkg->client) }}" class="font-medium hover:text-orange-500 transition" style="color: #0f2035;">
                    {{ $pkg->client->full_name }}
                </a>
                <span class="text-gray-500 text-sm">{{ __('app.sessions_left') }}:
                    <strong class="{{ $pkg->scheduled_count === 1 ? 'text-red-600' : 'text-orange-500' }}">
                        {{ $pkg->scheduled_count }}
                    </strong>
                </span>
                <span class="text-gray-400 text-xs">
                    <i class="fas fa-calendar-alt mr-1"></i>{{ __('app.payment_date') }}:
                    <span class="text-gray-600 font-medium">{{ $pkg->payment_date->format('d.m.Y') }} г.</span>
                </span>
            </div>
            <a href="{{ route('packages.create', $pkg->client) }}" class="text-sm text-white px-3 py-1 rounded-lg transition ml-3 flex-shrink-0" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                + Пакет
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Upcoming sessions -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3" style="background-color: #0f2035;">
        <h2 class="text-white font-semibold text-lg">
            <i class="fas fa-calendar-alt mr-2" style="color: #fb923c;"></i>{{ __('app.upcoming_sessions') }}
        </h2>
        {{-- Filter tabs --}}
        <div class="flex items-center gap-1 bg-white/10 rounded-lg p-1 flex-wrap">
            <a href="{{ route('dashboard', ['filter' => 'today']) }}"
               class="px-3 py-1 rounded-md text-sm font-medium transition {{ $filter === 'today' ? 'bg-orange-500 text-white' : 'text-gray-300 hover:text-white' }}">
               Сегодня
            </a>
            <a href="{{ route('dashboard', ['filter' => 'tomorrow']) }}"
               class="px-3 py-1 rounded-md text-sm font-medium transition {{ $filter === 'tomorrow' ? 'bg-orange-500 text-white' : 'text-gray-300 hover:text-white' }}">
               Завтра
            </a>
            <a href="{{ route('dashboard', ['filter' => 'week']) }}"
               class="px-3 py-1 rounded-md text-sm font-medium transition {{ $filter === 'week' ? 'bg-orange-500 text-white' : 'text-gray-300 hover:text-white' }}">
               Неделя
            </a>
            <a href="{{ route('dashboard', ['filter' => 'history']) }}"
               class="px-3 py-1 rounded-md text-sm font-medium transition {{ $filter === 'history' ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white' }}">
               История
            </a>
        </div>
    </div>
    @if($upcomingSessions->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-calendar-times text-5xl mb-3"></i>
            <p>Нет занятий</p>
        </div>
    @else
    @php
        $todaySessions2 = $upcomingSessions->filter(fn($s) => $s->scheduled_date->isToday());
        $tomorrowSessions = $upcomingSessions->filter(fn($s) => $s->scheduled_date->isTomorrow());
        $groupedByDay = $upcomingSessions->groupBy(fn($s) => $s->scheduled_date->toDateString());
        $dayNames = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
    @endphp

    {{-- Сегодня (только не в режиме недели) --}}
    @if($filter !== 'week' && $todaySessions2->isNotEmpty())
    <div class="px-4 py-2 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
        <span class="text-xs font-semibold text-orange-500 uppercase tracking-wide">{{ __('app.today') }} — {{ now()->format('d.m.Y') }}</span>
        <div class="flex items-center gap-2">
            <button id="btn-edit-mode" onclick="enterEditMode()"
                class="text-xs text-orange-400 px-2.5 py-1 rounded-lg border border-orange-200 hover:border-orange-400 hover:bg-orange-100 transition flex items-center gap-1">
                <i class="fas fa-edit text-xs"></i> {{ __('app.edit') }}
            </button>
            <div id="edit-mode-actions" class="hidden flex items-center gap-1">
                <button onclick="openDashAddModal('today')"
                    class="text-xs text-white px-2 py-1 rounded-lg bg-orange-500 hover:bg-orange-600 transition flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-plus text-xs"></i> {{ __('app.add') }}
                </button>
                <button id="btn-delete-selected" onclick="dashBulkDelete()" disabled
                    class="text-xs px-2 py-1 rounded-lg border border-red-400 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap">
                    <i class="fas fa-trash-alt text-xs"></i> <span id="dash-sel-count">0</span>
                </button>
                <button onclick="exitEditMode()"
                    class="text-xs text-white px-2 py-1 rounded-lg bg-green-600 hover:bg-green-700 transition flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-check text-xs"></i> {{ __('app.done') }}
                </button>
            </div>
        </div>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($todaySessions2 as $session)
        @php $isToday = true; $dayLabel = __('app.today_label'); $dateLabel = $session->scheduled_date->format('d.m.Y'); @endphp
        <div id="session-row-{{ $session->id }}"
             class="dash-session-row today-row px-4 py-3 flex items-center gap-3 transition {{ $session->status === 'completed' ? 'bg-green-50' : 'hover:bg-gray-50' }}">
            <input type="checkbox" class="dash-session-cb hidden w-4 h-4 rounded cursor-pointer shrink-0"
                   value="{{ $session->id }}" style="accent-color:#f97316;" onchange="dashUpdateTodayBar()">

                {{-- Галочка / кнопка отметки --}}
                @if($session->status === 'completed')
                    {{-- Уже отходил — зелёная галочка, кликабельна для отмены --}}
                    <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="scheduled">
                        <button type="submit"
                            title="Отменить отметку"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition"
                            style="background-color: #16a34a;"
                            onmouseover="this.style.backgroundColor='#15803d'"
                            onmouseout="this.style.backgroundColor='#16a34a'">
                            <i class="fas fa-check text-white text-sm"></i>
                        </button>
                    </form>
                @elseif($session->status === 'missed')
                    {{-- Пропустил — красный крест --}}
                    <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="scheduled">
                        <button type="submit"
                            title="Вернуть в запланированные"
                            class="w-10 h-10 rounded-full border-2 border-red-400 flex items-center justify-center hover:bg-red-50 transition">
                            <i class="fas fa-times text-red-500 text-sm"></i>
                        </button>
                    </form>
                @else
                    {{-- Запланировано — пустой круг, клик = пришёл --}}
                    <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit"
                            title="Отметить — клиент пришёл"
                            class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center hover:border-green-500 hover:bg-green-50 transition group">
                            <i class="fas fa-check text-gray-200 group-hover:text-green-500 text-sm transition"></i>
                        </button>
                    </form>
                @endif

                {{-- Время --}}
                <button type="button"
                    onclick="openTimePicker(this, '{{ route('sessions.update', $session) }}', '{{ $session->status }}', '{{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '' }}')"
                    class="shrink-0 text-center group cursor-pointer" style="width:56px;">
                    <p class="text-sm font-bold tabular-nums group-hover:opacity-70 transition"
                       style="letter-spacing:0.02em; {{ $session->scheduled_time ? 'color:#f97316;' : 'color:#94a3b8;' }}">
                        {{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '—:—' }}
                    </p>
                    <p class="text-xs {{ $isToday ? 'font-semibold' : 'text-gray-400' }}"
                       style="{{ $isToday ? 'color:#f97316;' : '' }}">
                        {{ $dayLabel }}
                    </p>
                </button>

                <div class="w-px h-10 bg-gray-200 shrink-0"></div>

                {{-- Клиент + кнопки в одном блоке --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        {{-- Имя --}}
                        <a href="{{ route('clients.show', $session->client) }}"
                           class="flex-1 min-w-0 font-medium hover:text-orange-500 transition truncate {{ $session->status === 'completed' ? 'line-through text-gray-400' : '' }}"
                           style="{{ $session->status !== 'completed' ? 'color:#0f2035;' : '' }}">
                            {{ $session->client->full_name }}
                        </a>
                        {{-- Стоимость --}}
                        @php
                            $pkg = $session->package;
                            $pricePerSession = ($pkg && $pkg->total_sessions > 0)
                                ? round($pkg->price / $pkg->total_sessions)
                                : 0;
                        @endphp
                        @if($pricePerSession > 0)
                        <span class="text-xs font-semibold shrink-0 hidden sm:inline {{ $session->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                            {{ number_format($pricePerSession, 0, '.', ' ') }} ₸
                        </span>
                        @endif
                        {{-- Статус + кнопки --}}
                        <div class="flex items-center gap-1 shrink-0">
                            @if($session->status === 'scheduled')
                                <form method="POST" action="{{ route('sessions.update', $session) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="missed">
                                    <button type="submit" title="{{ __('app.missed_short') }}"
                                        class="w-7 h-7 md:w-auto md:h-auto md:px-3 md:py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition flex items-center justify-center gap-1 text-xs">
                                        <i class="fas fa-user-times text-xs"></i>
                                        <span class="hidden md:inline">{{ __('app.missed_short') }}</span>
                                    </button>
                                </form>
                                <button type="button" title="{{ __('app.cancelled_short') }}"
                                    onclick="openDashReschedule({{ $session->id }}, '{{ $session->scheduled_date->format('d.m.Y') }}')"
                                    class="w-7 h-7 md:w-auto md:h-auto md:px-3 md:py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:border-purple-300 hover:text-purple-600 hover:bg-purple-50 transition flex items-center justify-center gap-1 text-xs">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    <span class="hidden md:inline">{{ __('app.cancelled_short') }}</span>
                                </button>
                            @elseif($session->status === 'completed')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium whitespace-nowrap">
                                    <i class="fas fa-check mr-1"></i><span class="hidden sm:inline">{{ __('app.came') }}</span>
                                </span>
                            @elseif($session->status === 'cancelled')
                                <span class="text-xs px-1.5 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            @else
                                <span class="text-xs px-1.5 py-1 rounded-full bg-red-100 text-red-600 font-medium">
                                    <i class="fas fa-times"></i>
                                </span>
                            @endif
                            <a href="{{ route('packages.sessions', $session->package) }}"
                               class="w-7 h-7 rounded-lg border border-gray-200 text-gray-400 hover:border-orange-400 hover:text-orange-500 transition flex items-center justify-center">
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $dateLabel }}</p>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Завтра (только в режиме filter=tomorrow) --}}
    @if($filter === 'tomorrow' && $tomorrowSessions->isNotEmpty())
    <div class="px-4 py-2 bg-blue-50 border-t border-b border-blue-100 flex items-center justify-between">
        <span class="text-xs font-semibold text-blue-400 uppercase tracking-wide">{{ __('app.tomorrow') }} — {{ now()->addDay()->format('d.m.Y') }}</span>
        <div class="flex items-center gap-2">
            <button id="btn-edit-tomorrow" onclick="enterTomorrowEditMode()"
                class="text-xs text-blue-400 px-2.5 py-1 rounded-lg border border-blue-200 hover:border-blue-400 hover:bg-blue-100 transition flex items-center gap-1">
                <i class="fas fa-edit text-xs"></i> {{ __('app.edit') }}
            </button>
            <div id="edit-tomorrow-actions" class="hidden flex items-center gap-1">
                <button onclick="openDashAddModal('tomorrow')"
                    class="text-xs text-white px-2 py-1 rounded-lg bg-blue-500 hover:bg-blue-600 transition flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-plus text-xs"></i> {{ __('app.add') }}
                </button>
                <button id="btn-delete-tomorrow" onclick="dashBulkDelete()" disabled
                    class="text-xs px-2 py-1 rounded-lg border border-red-400 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap">
                    <i class="fas fa-trash-alt text-xs"></i> <span id="dash-sel-count-tomorrow">0</span>
                </button>
                <button onclick="exitTomorrowEditMode()"
                    class="text-xs text-white px-2 py-1 rounded-lg bg-green-600 hover:bg-green-700 transition flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-check text-xs"></i> {{ __('app.done') }}
                </button>
            </div>
        </div>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($tomorrowSessions as $session)
        @php $isToday = false; $dayLabel = __('app.tomorrow_label'); $dateLabel = $session->scheduled_date->format('d.m.Y'); @endphp
        <div id="session-row-{{ $session->id }}"
             class="dash-session-row tomorrow-row px-4 py-3 flex items-center gap-3 transition {{ $session->status === 'completed' ? 'bg-green-50' : 'hover:bg-gray-50' }}">
            <input type="checkbox" class="dash-session-cb hidden w-4 h-4 rounded cursor-pointer shrink-0"
                   value="{{ $session->id }}" style="accent-color:#3b82f6;" onchange="dashUpdateTomorrowBar()">

            {{-- Галочка / кнопка отметки --}}
            @if($session->status === 'completed')
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="scheduled">
                    <button type="submit" title="Отменить отметку"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition"
                        style="background-color: #16a34a;"
                        onmouseover="this.style.backgroundColor='#15803d'"
                        onmouseout="this.style.backgroundColor='#16a34a'">
                        <i class="fas fa-check text-white text-sm"></i>
                    </button>
                </form>
            @elseif($session->status === 'missed')
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="scheduled">
                    <button type="submit" title="Вернуть в запланированные"
                        class="w-10 h-10 rounded-full border-2 border-red-400 flex items-center justify-center hover:bg-red-50 transition">
                        <i class="fas fa-times text-red-500 text-sm"></i>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" title="Отметить — клиент пришёл"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center hover:border-green-500 hover:bg-green-50 transition group">
                        <i class="fas fa-check text-gray-200 group-hover:text-green-500 text-sm transition"></i>
                    </button>
                </form>
            @endif

            {{-- Время --}}
            <button type="button"
                onclick="openTimePicker(this, '{{ route('sessions.update', $session) }}', '{{ $session->status }}', '{{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '' }}')"
                class="shrink-0 text-center group cursor-pointer" style="width:56px;">
                <p class="text-sm font-bold tabular-nums group-hover:opacity-70 transition"
                   style="letter-spacing:0.02em; {{ $session->scheduled_time ? 'color:#f97316;' : 'color:#94a3b8;' }}">
                    {{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '—:—' }}
                </p>
                <p class="text-xs text-gray-400">{{ $dayLabel }}</p>
            </button>

            <div class="w-px h-10 bg-gray-200 shrink-0"></div>

            {{-- Клиент + кнопки в одном блоке --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    {{-- Имя --}}
                    <a href="{{ route('clients.show', $session->client) }}"
                       class="flex-1 min-w-0 font-medium hover:text-orange-500 transition truncate {{ $session->status === 'completed' ? 'line-through text-gray-400' : '' }}"
                       style="{{ $session->status !== 'completed' ? 'color:#0f2035;' : '' }}">
                        {{ $session->client->full_name }}
                    </a>
                    {{-- Стоимость --}}
                    @php
                        $pkg = $session->package;
                        $pricePerSession = ($pkg && $pkg->total_sessions > 0) ? round($pkg->price / $pkg->total_sessions) : 0;
                    @endphp
                    @if($pricePerSession > 0)
                    <span class="text-xs font-semibold shrink-0 hidden sm:inline {{ $session->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                        {{ number_format($pricePerSession, 0, '.', ' ') }} ₸
                    </span>
                    @endif
                    {{-- Статус + кнопки --}}
                    <div class="flex items-center gap-1 shrink-0">
                        @if($session->status === 'scheduled')
                            <form method="POST" action="{{ route('sessions.update', $session) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="missed">
                                <button type="submit" title="{{ __('app.missed_short') }}"
                                    class="w-7 h-7 md:w-auto md:h-auto md:px-3 md:py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition flex items-center justify-center gap-1 text-xs">
                                    <i class="fas fa-user-times text-xs"></i>
                                    <span class="hidden md:inline">{{ __('app.missed_short') }}</span>
                                </button>
                            </form>
                            <button type="button" title="{{ __('app.cancelled_short') }}"
                                onclick="openDashReschedule({{ $session->id }}, '{{ $session->scheduled_date->format('d.m.Y') }}')"
                                class="w-7 h-7 md:w-auto md:h-auto md:px-3 md:py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:border-purple-300 hover:text-purple-600 hover:bg-purple-50 transition flex items-center justify-center gap-1 text-xs">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                <span class="hidden md:inline">{{ __('app.cancelled_short') }}</span>
                            </button>
                        @elseif($session->status === 'completed')
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium whitespace-nowrap">
                                <i class="fas fa-check mr-1"></i><span class="hidden sm:inline">{{ __('app.came') }}</span>
                            </span>
                        @elseif($session->status === 'cancelled')
                            <span class="text-xs px-1.5 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        @else
                            <span class="text-xs px-1.5 py-1 rounded-full bg-red-100 text-red-600 font-medium">
                                <i class="fas fa-times"></i>
                            </span>
                        @endif
                        <a href="{{ route('packages.sessions', $session->package) }}"
                           class="w-7 h-7 rounded-lg border border-gray-200 text-gray-400 hover:border-orange-400 hover:text-orange-500 transition flex items-center justify-center">
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $dateLabel }}</p>
            </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- История --}}
    @if($filter === 'history')
    @php
        $historyGrouped = $upcomingSessions->groupBy(fn($s) => $s->scheduled_date->toDateString());
        $ruDaysH = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
    @endphp
    @foreach($historyGrouped as $dateStr => $daySessions)
    @php $date = \Carbon\Carbon::parse($dateStr); @endphp
    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            {{ $ruDaysH[$date->format('D')] ?? '' }}, {{ $date->format('d.m.Y') }}
        </span>
        <span class="text-xs text-gray-400">{{ $daySessions->count() }} занятий</span>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($daySessions as $session)
        <div class="px-4 py-3 flex items-center gap-3 {{ $session->status === 'completed' ? 'bg-green-50' : ($session->status === 'missed' ? 'bg-red-50' : 'hover:bg-gray-50') }}">
            {{-- Статус иконка --}}
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                {{ $session->status === 'completed' ? '' : ($session->status === 'missed' ? 'border-2 border-red-300' : 'border-2 border-gray-200') }}"
                style="{{ $session->status === 'completed' ? 'background:#16a34a;' : '' }}">
                @if($session->status === 'completed')
                    <i class="fas fa-check text-white text-sm"></i>
                @elseif($session->status === 'missed')
                    <i class="fas fa-times text-red-400 text-sm"></i>
                @else
                    <i class="fas fa-circle text-gray-300 text-xs"></i>
                @endif
            </div>
            {{-- Время --}}
            <div class="shrink-0 text-center" style="width:56px;">
                <p class="text-sm font-bold tabular-nums" style="color:{{ $session->scheduled_time ? '#94a3b8' : '#cbd5e1' }};">
                    {{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '—:—' }}
                </p>
            </div>
            <div class="w-px h-10 bg-gray-200 shrink-0"></div>
            {{-- Клиент --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <a href="{{ route('clients.show', $session->client) }}"
                       class="font-medium hover:text-orange-500 transition truncate {{ $session->status === 'completed' ? 'line-through text-gray-400' : 'text-gray-700' }}">
                        {{ $session->client->full_name }}
                    </a>
                    @php $pkg = $session->package; $pps = ($pkg && $pkg->total_sessions > 0) ? round($pkg->price / $pkg->total_sessions) : 0; @endphp
                    @if($pps > 0)
                    <span class="text-xs font-semibold shrink-0 hidden sm:inline {{ $session->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                        {{ number_format($pps, 0, '.', ' ') }} ₸
                    </span>
                    @endif
                    <span class="text-xs px-2 py-0.5 rounded-full shrink-0
                        {{ $session->status === 'completed' ? 'bg-green-100 text-green-700' : ($session->status === 'missed' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500') }}">
                        {{ $session->status === 'completed' ? 'Пришёл' : ($session->status === 'missed' ? 'Пропуск' : 'Отменено') }}
                    </span>
                </div>
            </div>
            <a href="{{ route('packages.sessions', $session->package) }}"
               class="w-7 h-7 rounded-lg border border-gray-200 text-gray-400 hover:border-orange-400 hover:text-orange-500 transition flex items-center justify-center shrink-0">
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        @endforeach
    </div>
    @endforeach
    @endif

    {{-- Неделя: все 7 дней начиная с сегодня --}}
    @if($filter === 'week')
    @php
        $ruDaysFull = ['Mon'=>'Понедельник','Tue'=>'Вторник','Wed'=>'Среда','Thu'=>'Четверг','Fri'=>'Пятница','Sat'=>'Суббота','Sun'=>'Воскресенье'];
        $ruDaysShort = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
        $dayColors = ['Mon'=>'#3b82f6','Tue'=>'#8b5cf6','Wed'=>'#10b981','Thu'=>'#f59e0b','Fri'=>'#f97316','Sat'=>'#ec4899','Sun'=>'#6366f1'];
        $weekDates = collect(range(0,6))->map(fn($i) => \Carbon\Carbon::today()->addDays($i));
    @endphp
    @foreach($weekDates as $date)
    @php
        $dateStr = $date->toDateString();
        $dayKey = $date->format('D');
        $dayName = $ruDaysFull[$dayKey] ?? $dayKey;
        $dayColor = $dayColors[$dayKey] ?? '#6b7280';
        $isToday = $date->isToday();
        $daySessions = $groupedByDay->get($dateStr, collect());
    @endphp
    <div class="px-4 py-2 border-b flex items-center justify-between"
         style="background:{{ $isToday ? '#fff7ed' : '#f9fafb' }};border-color:{{ $isToday ? '#fed7aa' : '#f3f4f6' }};">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $dayColor }};"></span>
            <span class="text-xs font-semibold uppercase tracking-wide" style="color:{{ $dayColor }};">{{ $dayName }}</span>
            <span class="text-xs text-gray-400">— {{ $date->format('d.m.Y') }}</span>
            @if($isToday)<span class="text-xs bg-orange-500 text-white px-1.5 py-0.5 rounded-full ml-1">сегодня</span>@endif
        </div>
        <div class="flex items-center gap-2">
            @if($daySessions->count() > 0)
                <span class="text-xs text-gray-400">{{ $daySessions->count() }} занятий</span>
            @endif
            <button onclick="openWeekAddModal('{{ $dateStr }}', '{{ $date->format('d.m.Y') }}')"
                class="text-xs px-2 py-1 rounded-lg border border-dashed border-gray-300 text-gray-400 hover:border-orange-400 hover:text-orange-500 transition flex items-center gap-1">
                <i class="fas fa-plus text-xs"></i> Добавить
            </button>
        </div>
    </div>
    @if($daySessions->isEmpty())
    <div class="px-4 py-3 text-center text-xs text-gray-300 border-b border-gray-50">
        <i class="fas fa-calendar-day mr-1"></i> Нет занятий
    </div>
    @else
    <div class="divide-y divide-gray-50">
        @foreach($daySessions as $session)
        @php
            $dayLabel = $isToday ? 'Сегодня' : ($ruDaysShort[$session->scheduled_date->format('D')] ?? '');
            $dateLabel = $session->scheduled_date->format('d.m.Y');
        @endphp
        <div id="session-row-week-{{ $session->id }}"
             class="px-4 py-3 flex items-center gap-3 transition {{ $session->status === 'completed' ? 'bg-green-50' : 'hover:bg-gray-50' }}">
            @if($session->status === 'completed')
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="scheduled">
                    <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center transition" style="background-color:#16a34a;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                        <i class="fas fa-check text-white text-sm"></i>
                    </button>
                </form>
            @elseif($session->status === 'missed')
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="scheduled">
                    <button type="submit" class="w-10 h-10 rounded-full border-2 border-red-400 flex items-center justify-center hover:bg-red-50 transition">
                        <i class="fas fa-times text-red-500 text-sm"></i>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('sessions.update', $session) }}" class="shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center hover:border-green-500 hover:bg-green-50 transition group">
                        <i class="fas fa-check text-gray-200 group-hover:text-green-500 text-sm transition"></i>
                    </button>
                </form>
            @endif
            <button type="button"
                onclick="openTimePicker(this, '{{ route('sessions.update', $session) }}', '{{ $session->status }}', '{{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '' }}')"
                class="shrink-0 text-center group cursor-pointer" style="width:56px;">
                <p class="text-sm font-bold tabular-nums group-hover:opacity-70 transition"
                   style="letter-spacing:0.02em;{{ $session->scheduled_time ? 'color:#f97316;' : 'color:#94a3b8;' }}">
                    {{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '—:—' }}
                </p>
                <p class="text-xs {{ $isToday ? 'font-semibold' : 'text-gray-400' }}" style="{{ $isToday ? 'color:#f97316;' : '' }}">{{ $dayLabel }}</p>
            </button>
            <div class="w-px h-10 bg-gray-200 shrink-0"></div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('clients.show', $session->client) }}"
                       class="flex-1 min-w-0 font-medium hover:text-orange-500 transition truncate {{ $session->status === 'completed' ? 'line-through text-gray-400' : '' }}"
                       style="{{ $session->status !== 'completed' ? 'color:#0f2035;' : '' }}">
                        {{ $session->client->full_name }}
                    </a>
                    @php $pkg=$session->package; $pps=($pkg&&$pkg->total_sessions>0)?round($pkg->price/$pkg->total_sessions):0; @endphp
                    @if($pps > 0)
                    <span class="text-xs font-semibold shrink-0 hidden sm:inline {{ $session->status==='completed'?'text-green-600':'text-gray-400' }}">
                        {{ number_format($pps,0,'.', ' ') }} ₸
                    </span>
                    @endif
                    <div class="flex items-center gap-1 shrink-0">
                        @if($session->status === 'scheduled')
                            <form method="POST" action="{{ route('sessions.update', $session) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="missed">
                                <button type="submit" class="w-7 h-7 md:w-auto md:h-auto md:px-3 md:py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition flex items-center justify-center gap-1 text-xs">
                                    <i class="fas fa-user-times text-xs"></i><span class="hidden md:inline">Пропуск</span>
                                </button>
                            </form>
                        @elseif($session->status === 'completed')
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium whitespace-nowrap">
                                <i class="fas fa-check mr-1"></i><span class="hidden sm:inline">Пришёл</span>
                            </span>
                        @elseif($session->status === 'missed')
                            <span class="text-xs px-1.5 py-1 rounded-full bg-red-100 text-red-600 font-medium"><i class="fas fa-times"></i></span>
                        @endif
                        <a href="{{ route('packages.sessions', $session->package) }}"
                           class="w-7 h-7 rounded-lg border border-gray-200 text-gray-400 hover:border-orange-400 hover:text-orange-500 transition flex items-center justify-center">
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $dateLabel }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endforeach
    @endif

    @endif
</div>

{{-- Daily earnings summary --}}
<div class="mt-4 bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100" style="background-color: #0f2035;">
        <h2 class="text-white font-semibold text-lg">
            <i class="fas fa-wallet mr-2" style="color: #fb923c;"></i>{{ __('app.day_summary') }} — {{ now()->format('d.m.Y') }}
        </h2>
    </div>
    <div class="px-4 md:px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="grid grid-cols-3 gap-3 md:flex md:gap-6">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('app.sessions_conducted') }}</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 whitespace-nowrap" style="color:#0f2035;">{{ $todayCompletedCount }}</p>
            </div>
            <div class="hidden md:block w-px bg-gray-200"></div>
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('app.earned_today') }}</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 text-green-600 whitespace-nowrap">
                    {{ number_format($todayEarnings, 0, '.', ' ') }} ₸
                </p>
            </div>
            @if($todaySessions > $todayCompletedCount)
            <div class="hidden md:block w-px bg-gray-200"></div>
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('app.pending_earnings') }}</p>
                @php
                    $remainingToday = $upcomingSessions
                        ->where('status', 'scheduled')
                        ->filter(fn($s) => $s->scheduled_date->isToday());
                    $pendingEarnings = $remainingToday->sum(function($s) {
                        $p = $s->package;
                        return ($p && $p->total_sessions > 0) ? round($p->price / $p->total_sessions) : 0;
                    });
                @endphp
                <p class="text-2xl md:text-3xl font-bold mt-1 text-blue-500 whitespace-nowrap">
                    {{ number_format($pendingEarnings, 0, '.', ' ') }} ₸
                </p>
            </div>
            @endif
        </div>

        @if($todaySessions > 0)
        <div class="text-right sm:text-right">
            <p class="text-xs text-gray-400 mb-1">{{ __('app.day_progress') }}</p>
            <div class="w-full sm:w-48 bg-gray-200 rounded-full h-2.5">
                @php $dayProgress = $todaySessions > 0 ? round(($todayCompletedCount / $todaySessions) * 100) : 0; @endphp
                <div class="h-2.5 rounded-full transition-all" style="width:{{ $dayProgress }}%; background:linear-gradient(to right,#fb923c,#16a34a);"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                {{ $todayCompletedCount }}
                {{ str_replace(':total', $todaySessions, __('app.of_trainings')) }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- Hidden delete form --}}
<form id="dash-delete-form" method="POST" action="{{ route('sessions.bulkDelete') }}" class="hidden">
    @csrf @method('DELETE')
    <div id="dash-delete-ids"></div>
</form>

{{-- Add session modal --}}
<div id="dash-add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4">
        <h3 class="text-lg font-bold mb-4" style="color:#0f2035;">
            <i class="fas fa-plus-circle text-orange-400 mr-2"></i>{{ __('app.add_session') }}
        </h3>

        {{-- Дата (только отображение, задаётся при открытии) --}}
        <div class="mb-4 px-4 py-2.5 rounded-xl flex items-center gap-2" id="dash-date-badge" style="background:#fff7ed;">
            <i class="fas fa-calendar-alt" style="color:#f97316;"></i>
            <span class="text-sm font-semibold" id="dash-date-label" style="color:#f97316;">Сегодня</span>
            <span class="text-sm text-gray-400" id="dash-date-value">{{ now()->format('d.m.Y') }}</span>
        </div>

        {{-- Выбор клиента --}}
        <form id="dash-add-form" method="POST" action="">
            @csrf
            <input type="hidden" name="scheduled_date" id="dash-add-date" value="{{ now()->format('Y-m-d') }}">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Клиент</label>
                <select id="dash-add-client" name="_client_pkg"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    onchange="dashClientChanged(this)">
                    <option value="">{{ __('app.select_client') }}</option>
                    @foreach($addClients as $c)
                        @php $pkg = $c->packages->first() @endphp
                        @if($pkg)
                            <option value="{{ $pkg->id }}">{{ $c->full_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.time_optional') }}</label>
                <input type="hidden" name="scheduled_time" id="dash-add-time">
                <button type="button" id="dash-add-time-btn" onclick="openDashTimePicker(this)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-left flex items-center justify-between hover:border-orange-400 transition text-sm bg-white">
                    <span id="dash-add-time-label" class="text-gray-400">Выбрать время</span>
                    <i class="fas fa-clock text-gray-400 text-xs"></i>
                </button>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="submitDashAdd()"
                    class="flex-1 text-white py-2 rounded-lg font-medium transition text-sm"
                    style="background-color:#f97316"
                    onmouseover="this.style.backgroundColor='#ea580c'"
                    onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-plus mr-1"></i>Добавить
                </button>
                <button type="button" onclick="closeDashAddModal()"
                    class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reschedule modal for dashboard --}}
<div id="dash-reschedule-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4">
        <h3 class="text-lg font-bold mb-1" style="color:#0f2035;">
            <i class="fas fa-calendar-alt text-orange-400 mr-2"></i>Перенос занятия
        </h3>
        <p class="text-sm text-gray-400 mb-4" id="dash-modal-subtitle">Клиент отменил тренировку</p>
        <form id="dash-reschedule-form" method="POST">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Перенести на дату</label>
                    <input type="date" name="new_date" id="dash-reschedule-date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.time_optional') }}</label>
                    <input type="time" name="new_time" id="dash-reschedule-time"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="submit"
                    class="flex-1 text-white py-2 rounded-lg font-medium transition"
                    style="background-color:#f97316"
                    onmouseover="this.style.backgroundColor='#ea580c'"
                    onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-calendar-check mr-1"></i>Перенести
                </button>
                <button type="button" onclick="closeDashReschedule()"
                    class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openWeekAddModal(dateVal, dateFmt) {
    document.getElementById('dash-add-date').value = dateVal;
    document.getElementById('dash-date-value').textContent = dateFmt;
    const badge = document.getElementById('dash-date-badge');
    const label = document.getElementById('dash-date-label');
    const today = new Date().toISOString().split('T')[0];
    label.textContent = dateVal === today ? 'Сегодня' : dateFmt;
    label.style.color = '#f97316';
    badge.style.background = '#fff7ed';
    const sel = document.getElementById('dash-add-client');
    sel.value = ''; sel.style.borderColor = '';
    document.getElementById('dash-add-time').value = '';
    const lbl = document.getElementById('dash-add-time-label');
    lbl.textContent = 'Выбрать время'; lbl.style.color = '';
    document.getElementById('dash-add-form').action = '';
    document.getElementById('dash-add-modal').classList.remove('hidden');
}

function openDashReschedule(sessionId, dateLabel) {
    document.getElementById('dash-reschedule-form').action = '/sessions/' + sessionId + '/reschedule';
    document.getElementById('dash-modal-subtitle').textContent = 'Клиент отменил занятие от ' + dateLabel;
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('dash-reschedule-date').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('dash-reschedule-modal').classList.remove('hidden');
}
function closeDashReschedule() {
    document.getElementById('dash-reschedule-modal').classList.add('hidden');
}
document.getElementById('dash-reschedule-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDashReschedule();
});

// ---- Dashboard edit mode ----
// Режим редактирования "Сегодня"
function enterEditMode() {
    document.getElementById('btn-edit-mode').classList.add('hidden');
    document.getElementById('edit-mode-actions').classList.remove('hidden');
    document.querySelectorAll('.today-row .dash-session-cb').forEach(cb => cb.classList.remove('hidden'));
}

function exitEditMode() {
    document.getElementById('btn-edit-mode').classList.remove('hidden');
    document.getElementById('edit-mode-actions').classList.add('hidden');
    document.querySelectorAll('.today-row .dash-session-cb').forEach(cb => {
        cb.classList.add('hidden');
        cb.checked = false;
    });
    document.querySelectorAll('.today-row').forEach(r => r.style.background = '');
    dashUpdateTodayBar();
}

function dashUpdateTodayBar() {
    const count = document.querySelectorAll('.today-row .dash-session-cb:checked').length;
    const el = document.getElementById('dash-sel-count');
    if (el) el.textContent = count;
    const btn = document.getElementById('btn-delete-selected');
    if (btn) btn.disabled = count === 0;
    document.querySelectorAll('.today-row .dash-session-cb').forEach(cb => {
        const row = cb.closest('.today-row');
        if (row) row.style.background = cb.checked ? '#fff7ed' : '';
    });
}

// Режим редактирования "Завтра"
function enterTomorrowEditMode() {
    document.getElementById('btn-edit-tomorrow').classList.add('hidden');
    document.getElementById('edit-tomorrow-actions').classList.remove('hidden');
    document.querySelectorAll('.tomorrow-row .dash-session-cb').forEach(cb => cb.classList.remove('hidden'));
}

function exitTomorrowEditMode() {
    document.getElementById('btn-edit-tomorrow').classList.remove('hidden');
    document.getElementById('edit-tomorrow-actions').classList.add('hidden');
    document.querySelectorAll('.tomorrow-row .dash-session-cb').forEach(cb => {
        cb.classList.add('hidden');
        cb.checked = false;
    });
    document.querySelectorAll('.tomorrow-row').forEach(r => r.style.background = '');
    dashUpdateTomorrowBar();
}

function dashUpdateTomorrowBar() {
    const count = document.querySelectorAll('.tomorrow-row .dash-session-cb:checked').length;
    const el = document.getElementById('dash-sel-count-tomorrow');
    if (el) el.textContent = count;
    const btn = document.getElementById('btn-delete-tomorrow');
    if (btn) btn.disabled = count === 0;
    document.querySelectorAll('.tomorrow-row .dash-session-cb').forEach(cb => {
        const row = cb.closest('.tomorrow-row');
        if (row) row.style.background = cb.checked ? '#eff6ff' : '';
    });
}

function dashBulkDelete() {
    const ids = Array.from(document.querySelectorAll('.dash-session-cb:checked')).map(c => c.value);
    if (!ids.length) return;
    if (!confirm('Удалить ' + ids.length + ' занятий?')) return;

    const container = document.getElementById('dash-delete-ids');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'session_ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('dash-delete-form').submit();
}

// ---- Add session modal ----
function openDashAddModal(day) {
    const today    = new Date();
    const tomorrow = new Date(); tomorrow.setDate(today.getDate() + 1);
    const d   = day === 'today' ? today : tomorrow;
    const val = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    const fmt = String(d.getDate()).padStart(2,'0') + '.' + String(d.getMonth()+1).padStart(2,'0') + '.' + d.getFullYear();

    document.getElementById('dash-add-date').value  = val;
    document.getElementById('dash-date-value').textContent = fmt;

    const badge = document.getElementById('dash-date-badge');
    const label = document.getElementById('dash-date-label');
    if (day === 'today') {
        label.textContent = 'Сегодня';
        badge.style.background = '#fff7ed';
        label.style.color = '#f97316';
    } else {
        label.textContent = 'Завтра';
        badge.style.background = '#eff6ff';
        label.style.color = '#3b82f6';
    }

    // Reset form
    const sel = document.getElementById('dash-add-client');
    sel.value = '';
    sel.style.borderColor = '';
    document.getElementById('dash-add-time').value = '';
    const lbl = document.getElementById('dash-add-time-label');
    lbl.textContent = 'Выбрать время'; lbl.style.color = '';
    document.getElementById('dash-add-form').action = '';
    document.getElementById('dash-add-modal').classList.remove('hidden');
}

function closeDashAddModal() {
    document.getElementById('dash-add-modal').classList.add('hidden');
}

function dashClientChanged(sel) {
    const form = document.getElementById('dash-add-form');
    if (sel.value) {
        form.action = '/packages/' + sel.value + '/sessions';
        sel.style.borderColor = '';
    }
}

function openDashTimePicker(btn) {
    if (window._gTimePicker) { window._gTimePicker.remove(); window._gTimePicker = null; }
    const current = document.getElementById('dash-add-time').value;
    let _h = current ? parseInt(current.split(':')[0]) : 9;
    let _m = current ? parseInt(current.split(':')[1]) : 0;
    const hours = Array.from({length:24}, (_,i) => i);
    const minutes = [0,5,10,15,20,25,30,35,40,45,50,55];
    const colStyle = 'flex:1;overflow-y:auto;text-align:center;padding:4px 8px;';
    const iS = (sel) => `padding:8px 0;font-size:17px;font-weight:${sel?'700':'400'};color:${sel?'#f97316':'#374151'};cursor:pointer;border-radius:6px;background:${sel?'#fff7ed':'transparent'};`;
    const popup = document.createElement('div');
    popup.id = '_gTimePicker';
    popup.style.cssText = 'position:fixed;z-index:3000;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);overflow:hidden;width:200px;';
    popup.innerHTML = `
        <div style="background:#0f2035;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#fff;font-size:13px;font-weight:600;">Время занятия</span>
            <span id="datp-preview" style="color:#fb923c;font-size:15px;font-weight:700;">${String(_h).padStart(2,'0')}:${String(_m).padStart(2,'0')}</span>
        </div>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;">
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;border-right:1px solid #f3f4f6;">Часы</div>
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;">Минуты</div>
        </div>
        <div style="display:flex;height:210px;">
            <div id="datp-hours" style="${colStyle}border-right:1px solid #f3f4f6;">
                ${hours.map(v=>`<div data-v="${v}" onclick="datpSelect('h',${v},this)" style="${iS(v===_h)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
            <div id="datp-mins" style="${colStyle}">
                ${minutes.map(v=>`<div data-v="${v}" onclick="datpSelect('m',${v},this)" style="${iS(v===_m)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
        </div>
        <div style="padding:10px 12px;">
            <button type="button" onclick="datpConfirm()" style="width:100%;background:#f97316;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;">✓ Сохранить</button>
        </div>`;
    document.body.appendChild(popup);
    window._gTimePicker = popup;
    window._datpH = _h; window._datpM = _m;
    const rect = btn.getBoundingClientRect();
    let top = rect.bottom + 6;
    if (top + 340 > window.innerHeight - 10) top = rect.top - 340 - 6;
    let left = rect.left;
    if (left + 200 > window.innerWidth - 10) left = window.innerWidth - 210;
    if (left < 8) left = 8;
    popup.style.top = top + 'px'; popup.style.left = left + 'px';
    setTimeout(() => {
        popup.querySelector(`#datp-hours [data-v="${_h}"]`)?.scrollIntoView({block:'center'});
        popup.querySelector(`#datp-mins [data-v="${_m}"]`)?.scrollIntoView({block:'center'});
    }, 10);
    setTimeout(() => {
        document.addEventListener('click', function _h(e) {
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.remove(); window._gTimePicker = null;
                document.removeEventListener('click', _h);
            }
        });
    }, 100);
}
function datpSelect(type, val, el) {
    const colId = type === 'h' ? 'datp-hours' : 'datp-mins';
    document.querySelectorAll('#'+colId+' div').forEach(d => { d.style.fontWeight='400'; d.style.color='#374151'; d.style.background='transparent'; });
    el.style.fontWeight='700'; el.style.color='#f97316'; el.style.background='#fff7ed';
    if (type==='h') window._datpH=val; else window._datpM=val;
    document.getElementById('datp-preview').textContent = String(window._datpH).padStart(2,'0')+':'+String(window._datpM).padStart(2,'0');
}
function datpConfirm() {
    const t = String(window._datpH).padStart(2,'0')+':'+String(window._datpM).padStart(2,'0');
    document.getElementById('dash-add-time').value = t;
    const lbl = document.getElementById('dash-add-time-label');
    lbl.textContent = t; lbl.style.color = '#f97316';
    if (window._gTimePicker) { window._gTimePicker.remove(); window._gTimePicker = null; }
}

function submitDashAdd() {
    const sel  = document.getElementById('dash-add-client');
    const form = document.getElementById('dash-add-form');
    if (!sel.value) {
        sel.style.borderColor = '#f97316';
        sel.focus();
        return;
    }
    form.action = '/packages/' + sel.value + '/sessions';
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('dash-add-modal');
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === this) closeDashAddModal();
        });
    }
});

// Countdown to next auto-refresh
let seconds = 60;
const el = document.getElementById('countdown');
if (el) {
    setInterval(() => {
        seconds--;
        if (seconds <= 0) seconds = 60;
        el.textContent = seconds;
        if (seconds <= 10) el.style.color = '#f97316';
        else el.style.color = '';
    }, 1000);
}
</script>
@endsection
