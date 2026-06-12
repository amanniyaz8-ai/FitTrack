@extends('layouts.app')
@section('title', 'Статистика')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold" style="color: #0f2035;">{{ __('app.statistics_title') }}</h1>
    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ __('app.data_from_monitoring') }}</span>
</div>

{{-- === 3 КАРТОЧКИ === --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4" style="border-color:#f97316;">
        <p class="text-gray-500 text-sm mb-1">{{ __('app.paid_this_month') }}</p>
        <p class="text-4xl font-bold" style="color:#f97316;">{{ number_format($paidThisMonth, 0, '.', ' ') }} ₸</p>
        <p class="text-xs text-gray-400 mt-2">{{ now()->locale(app()->getLocale() === 'kk' ? 'kk' : 'ru')->translatedFormat('F Y') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm mb-1">Проведено тренировок в этом месяце</p>
        <p class="text-4xl font-bold text-green-600">{{ $sessionsThisMonth }}</p>
        <p class="text-xs mt-2 {{ $sessionsThisMonth >= $sessionsLastMonth ? 'text-green-500' : 'text-red-400' }}">
            <i class="fas fa-{{ $sessionsThisMonth >= $sessionsLastMonth ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
            {{ $sessionsLastMonth }} {{ __('app.last_month') }}
        </p>
    </div>
</div>

{{-- === ЗАРАБОТОК ПО ПЕРИОДАМ === --}}
<div class="bg-white rounded-xl shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100" style="background-color:#0f2035;">
        <h2 class="text-white font-semibold text-lg">
            <i class="fas fa-wallet mr-2" style="color:#fb923c;"></i>{{ __('app.earnings') }}
        </h2>
    </div>
    {{-- Фильтр по произвольным датам --}}
    <div class="px-6 py-5">
        <p class="text-sm font-medium text-gray-600 mb-3"><i class="fas fa-filter mr-1" style="color:#f97316;"></i>{{ __('app.select_period') }}</p>
        <form method="GET" action="{{ route('statistics') }}" class="flex flex-wrap items-end gap-2 w-full">
            <div>
                <label class="block text-xs text-gray-400 mb-1">{{ __('app.from') }}</label>
                <input type="date" name="from" value="{{ $filterFrom }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">{{ __('app.to') }}</label>
                <input type="date" name="to" value="{{ $filterTo }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <button type="submit"
                class="text-white px-4 py-2 rounded-lg text-sm transition"
                style="background-color:#f97316;"
                onmouseover="this.style.backgroundColor='#ea580c'"
                onmouseout="this.style.backgroundColor='#f97316'">
                <i class="fas fa-search mr-1"></i>{{ __('app.show') }}
            </button>
            {{-- Быстрые кнопки --}}
            <div class="flex gap-2 ml-2">
                <a href="{{ route('statistics', ['from' => now()->startOfWeek()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}"
                   class="text-xs px-3 py-2 rounded-lg border border-gray-200 text-gray-500 hover:border-orange-400 hover:text-orange-500 transition">
                    {{ __('app.week') }}
                </a>
                <a href="{{ route('statistics', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}"
                   class="text-xs px-3 py-2 rounded-lg border border-gray-200 text-gray-500 hover:border-orange-400 hover:text-orange-500 transition">
                    {{ __('app.month') }}
                </a>
                <a href="{{ route('statistics', ['from' => now()->startOfYear()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}"
                   class="text-xs px-3 py-2 rounded-lg border border-gray-200 text-gray-500 hover:border-orange-400 hover:text-orange-500 transition">
                    {{ __('app.year') }}
                </a>
            </div>
        </form>

        {{-- Результат фильтра --}}
        <div class="mt-4 p-4 rounded-xl flex items-center justify-between gap-6" style="background:linear-gradient(135deg,#fff7ed,#fef3c7);">
            <div>
                <p class="text-xs text-gray-500">
                    Период: <span class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($filterFrom)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($filterTo)->format('d.m.Y') }}
                    </span>
                </p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $sessionsCustom }} {{ __('app.sessions_done') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">{{ __('app.earned') }}</p>
                <p class="text-2xl md:text-3xl font-bold whitespace-nowrap" style="color:#f97316;">{{ number_format($earningsCustom, 0, '.', ' ') }} ₸</p>
            </div>
        </div>
    </div>
</div>

{{-- === ДИНАМИКА ПО МЕСЯЦАМ === --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background-color:#0f2035;">
            <h2 class="text-white font-semibold">
                <i class="fas fa-chart-bar mr-2" style="color:#fb923c;"></i>{{ __('app.sessions_by_month') }}
            </h2>
        </div>
        <div class="px-6 py-5">
            @php $maxCount = $monthlyStats->max('count') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($monthlyStats as $row)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">{{ $row['month'] }}</span>
                        <span class="font-semibold" style="color:#0f2035;">{{ $row['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all"
                             style="width:{{ $maxCount > 0 ? round($row['count'] / $maxCount * 100) : 0 }}%; background:linear-gradient(to right,#fb923c,#ea580c);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background-color:#0f2035;">
            <h2 class="text-white font-semibold">
                <i class="fas fa-chart-line mr-2" style="color:#fb923c;"></i>{{ __('app.earnings_by_month') }}
            </h2>
        </div>
        <div class="px-6 py-5">
            @php $maxEarned = $monthlyEarnings->max('earned') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($monthlyEarnings as $row)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">{{ $row['month'] }}</span>
                        <span class="font-semibold text-green-600">{{ number_format($row['earned'], 0, '.', ' ') }} ₸</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all"
                             style="width:{{ $maxEarned > 0 ? round($row['earned'] / $maxEarned * 100) : 0 }}%; background:linear-gradient(to right,#22c55e,#16a34a);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- === РЕЙТИНГ КЛИЕНТОВ === --}}
<div class="bg-white rounded-xl shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100" style="background-color:#0f2035;">
        <h2 class="text-white font-semibold text-lg">
            <i class="fas fa-trophy mr-2" style="color:#fb923c;"></i>{{ __('app.client_ranking') }}
        </h2>
    </div>

    @if($bestClient)
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl shrink-0"
                 style="background:linear-gradient(135deg,#f97316,#ea580c);">
                🏆
            </div>
            <div class="flex-1">
                <p class="text-xs text-orange-500 font-semibold uppercase tracking-wide mb-0.5">{{ __('app.best_client') }}</p>
                <a href="{{ route('clients.show', $bestClient['client']) }}"
                   class="text-xl font-bold hover:text-orange-500 transition" style="color:#0f2035;">
                    {{ $bestClient['client']->full_name }}
                </a>
                <div class="flex flex-wrap gap-3 mt-2 text-sm">
                    <span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>{{ $bestClient['completed'] }} {{ __('app.trainings') }}</span>
                    <span class="text-gray-400"><i class="fas fa-times-circle mr-1 text-red-400"></i>{{ $bestClient['missed'] }} {{ __('app.skips') }}</span>
                    <span class="text-gray-400"><i class="fas fa-calendar-times mr-1 text-purple-400"></i>{{ $bestClient['cancelled'] }} {{ __('app.cancels') }}</span>
                    <span class="text-blue-500"><i class="fas fa-credit-card mr-1"></i>{{ $bestClient['paid_pkgs'] }}/{{ $bestClient['total_pkgs'] }} {{ __('app.packages_paid') }}</span>
                </div>
            </div>
            <div class="text-center shrink-0">
                <p class="text-3xl font-bold" style="color:#f97316;">{{ $bestClient['attend_pct'] }}%</p>
                <p class="text-xs text-gray-400">{{ __('app.attendance') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="divide-y divide-gray-50">
        @foreach($clientRanks as $i => $row)
        <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition">
            <div class="w-8 text-center shrink-0">
                @if($i === 0) <span class="text-xl">🥇</span>
                @elseif($i === 1) <span class="text-xl">🥈</span>
                @elseif($i === 2) <span class="text-xl">🥉</span>
                @else <span class="text-gray-400 font-bold text-sm">{{ $i + 1 }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('clients.show', $row['client']) }}"
                   class="font-medium hover:text-orange-500 transition truncate block" style="color:#0f2035;">
                    {{ $row['client']->full_name }}
                </a>
                <div class="flex flex-wrap gap-x-2 gap-y-0.5 mt-1 text-xs text-gray-400">
                    <span class="text-green-600"><i class="fas fa-check mr-0.5"></i>{{ $row['completed'] }}</span>
                    <span class="text-red-400"><i class="fas fa-times mr-0.5"></i>{{ $row['missed'] }}</span>
                    <span class="text-purple-400"><i class="fas fa-calendar-times mr-0.5"></i>{{ $row['cancelled'] }}</span>
                    <span class="text-blue-400"><i class="fas fa-credit-card mr-0.5"></i>{{ $row['paid_pkgs'] }}/{{ $row['total_pkgs'] }}</span>
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="flex items-center gap-1.5">
                    <div class="hidden sm:block w-20 bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full"
                             style="width:{{ $row['attend_pct'] }}%; background:linear-gradient(to right,#fb923c,#16a34a);"></div>
                    </div>
                    <span class="text-sm font-semibold" style="color:#0f2035;">{{ $row['attend_pct'] }}%</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5 text-right">{{ __('app.attendance') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
