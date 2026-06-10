@extends('layouts.app')
@section('title', $client->full_name)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl md:text-2xl font-bold" style="color: #0f2035;">{{ $client->full_name }}</h1>
            @if($client->phone)
                <p class="text-gray-500 text-sm"><i class="fas fa-phone mr-1"></i>{{ $client->phone }}</p>
            @endif
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('packages.create', $client) }}" class="text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
            <i class="fas fa-plus"></i> Добавить пакет
        </a>
        <a href="{{ route('clients.edit', $client) }}" class="border border-gray-300 text-gray-500 px-3 py-2 rounded-lg text-sm hover:bg-gray-50 transition" title="Редактировать профиль клиента">
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
                <p class="font-medium" style="color: #0f2035;">{{ $client->training_days_label }}</p>
            </div>
            @if($client->goal)
            <div>
                <span class="text-gray-400 block">Цель:</span>
                <p class="text-gray-700">{{ $client->goal }}</p>
            </div>
            @endif
            @if($client->contraindications)
            <div>
                <span class="text-gray-400 block">Противопоказания:</span>
                <p class="text-red-600 bg-red-50 rounded p-2">{{ $client->contraindications }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="md:col-span-2 grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold" style="color: #0f2035;">{{ $packages->count() }}</p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Пакетов</p>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $packages->sum('completed_count') }}</p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Отходил</p>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
            <p class="text-2xl md:text-3xl font-bold text-red-500">{{ $packages->sum('missed_count') }}</p>
            <p class="text-gray-400 text-xs md:text-sm mt-1">Пропустил</p>
        </div>
    </div>
</div>

<!-- Packages -->
<h2 class="text-lg font-semibold mb-4" style="color: #0f2035;">
    <i class="fas fa-box-open mr-2" style="color: #fb923c;"></i>Пакеты / Абонементы
</h2>

@if($packages->isEmpty())
<div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
    <i class="fas fa-box-open text-5xl mb-3"></i>
    <p>Пакетов пока нет</p>
    <a href="{{ route('packages.create', $client) }}" class="mt-3 inline-block text-white px-5 py-2 rounded-lg transition text-sm" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
        Добавить пакет
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($packages as $pkg)
    @php
        $remaining = $pkg->total_sessions - $pkg->completed_count - $pkg->missed_count;
        $isArchived = $remaining <= 0;
        $progressPercent = $pkg->total_sessions > 0 ? (int) round(($pkg->completed_count / $pkg->total_sessions) * 100) : 0;
        $formattedPrice = number_format((float) $pkg->price, 0, '.', ' ') . ' ₸';
    @endphp
    <div class="bg-white rounded-xl shadow overflow-hidden {{ $isArchived ? 'opacity-70' : '' }}">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                @if($isArchived)
                    <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full font-medium">Архив</span>
                @else
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Активный</span>
                @endif
                <span class="font-medium" style="color: #0f2035;">{{ $pkg->total_sessions }} тренировок</span>
                <span class="text-gray-400 text-sm">{{ $formattedPrice }}</span>
                @if($pkg->is_paid)
                    <span class="text-green-600 text-xs"><i class="fas fa-check-circle"></i> Оплачен</span>
                @else
                    <span class="text-red-500 text-xs"><i class="fas fa-times-circle"></i> Не оплачен</span>
                @endif
                <span class="text-gray-400 text-xs">{{ $pkg->payment_date->format('d.m.Y') }}</span>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('packages.edit', $pkg) }}"
                   class="text-sm border border-gray-300 text-gray-500 px-2 md:px-3 py-1.5 rounded-lg hover:bg-gray-50 hover:border-orange-400 hover:text-orange-500 transition"
                   title="Редактировать пакет">
                    <i class="fas fa-edit md:mr-1"></i><span class="hidden md:inline">Редактировать</span>
                </a>
                <a href="{{ route('packages.sessions', $pkg) }}" class="text-sm text-white px-3 md:px-4 py-1.5 rounded-lg transition whitespace-nowrap" style="background-color: #0f2035;" onmouseover="this.style.backgroundColor='#162d4a'" onmouseout="this.style.backgroundColor='#0f2035'">
                    Занятия
                </a>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between text-xs md:text-sm mb-2">
                <div class="flex gap-2 md:gap-4 flex-wrap">
                    <span class="text-green-600"><i class="fas fa-check mr-1"></i>{{ $pkg->completed_count }} выполнено</span>
                    <span class="text-red-500"><i class="fas fa-times mr-1"></i>{{ $pkg->missed_count }} пропущено</span>
                    <span class="text-blue-600"><i class="fas fa-clock mr-1"></i>{{ $remaining }} осталось</span>
                </div>
                <span class="text-gray-400 ml-2 shrink-0">{{ $progressPercent }}%</span>
            </div>
            <!-- Progress bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all" style="width: {{ $progressPercent }}%; background: linear-gradient(to right, #fb923c, #ea580c);"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
