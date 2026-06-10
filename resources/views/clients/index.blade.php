@extends('layouts.app')
@section('title', 'Клиенты')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <form method="GET" class="flex-1 max-w-md">
        <div class="relative">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="{{ app()->getLocale() === 'kk' ? 'Іздеу...' : 'Поиск по имени...' }}"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2" style="--tw-ring-color: #fb923c;">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </form>
    <a href="{{ route('clients.create') }}" class="text-white px-5 py-2 rounded-lg font-medium transition flex items-center gap-2 w-fit" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
        {{ __('app.add_client') }}
    </a>
</div>

@if($activeClients->isEmpty() && $inactiveClients->isEmpty() && $unpaidClients->isEmpty())
    <div class="text-center py-16 text-gray-400 bg-white rounded-xl shadow">
        <i class="fas fa-user-plus text-6xl mb-4"></i>
        <p class="text-lg">Клиентов пока нет</p>
        <a href="{{ route('clients.create') }}" class="mt-4 inline-block text-white px-6 py-2 rounded-lg transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
            Добавить первого клиента
        </a>
    </div>
@else

{{-- === АКТИВНЫЕ === --}}
@if($activeClients->isNotEmpty())
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
        <h2 class="text-base font-semibold" style="color:#0f2035;">{{ __('app.active_clients') }}</h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $activeClients->count() }}</span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($activeClients as $client)
        @include('clients._card', ['client' => $client, 'isActive' => true])
        @endforeach
    </div>
</div>
@endif

{{-- === НЕ ОПЛАЧЕНО === --}}
@if($unpaidClients->isNotEmpty())
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
        <h2 class="text-base font-semibold text-yellow-700">{{ __('app.unpaid_clients') }}</h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $unpaidClients->count() }}</span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 opacity-90">
        @foreach($unpaidClients as $client)
        @include('clients._card', ['client' => $client, 'isActive' => false])
        @endforeach
    </div>
</div>
@endif

{{-- === НЕАКТИВНЫЕ === --}}
@if($inactiveClients->isNotEmpty())
<div>
    <div class="flex items-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        <h2 class="text-base font-semibold text-gray-500">{{ __('app.inactive_clients') }}</h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $inactiveClients->count() }}</span>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 opacity-75">
        @foreach($inactiveClients as $client)
        @include('clients._card', ['client' => $client, 'isActive' => false])
        @endforeach
    </div>
</div>
@endif

@endif
@endsection
