@extends('layouts.app')
@section('title', 'Техподдержка')

@section('content')

<div class="max-w-2xl mx-auto">

    <h1 class="text-xl font-bold mb-6" style="color:#0f2035;">
        <i class="fas fa-headset mr-2" style="color:#f97316;"></i>Техническая поддержка
    </h1>

    {{-- Success --}}
    @if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        <i class="fas fa-check-circle mr-2"></i>Обращение отправлено! Мы ответим в ближайшее время.
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="font-semibold mb-4" style="color:#0f2035;">Новое обращение</h2>
        <form method="POST" action="{{ route('support.cabinet.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Вы пишете как</label>
                <div class="px-3 py-2.5 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-700">
                    <i class="fas fa-user mr-2 text-gray-400"></i>{{ $user->name }} · {{ $user->email }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Опишите проблему <span class="text-red-500">*</span></label>
                <textarea name="message" rows="5" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none @error('message') border-red-400 @enderror"
                    placeholder="Опишите что произошло, когда возникла проблема...">{{ old('message') }}</textarea>
                @error('message')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full text-white py-3 rounded-lg font-semibold text-sm transition"
                style="background:#f97316;"
                onmouseover="this.style.background='#ea580c'"
                onmouseout="this.style.background='#f97316'">
                <i class="fas fa-paper-plane mr-2"></i>Отправить обращение
            </button>
        </form>
    </div>

    {{-- Ticket history --}}
    @if($tickets->count() > 0)
    <h2 class="font-semibold mb-3" style="color:#0f2035;">
        <i class="fas fa-history mr-2" style="color:#f97316;"></i>Мои обращения
    </h2>
    <div class="space-y-3">
        @foreach($tickets as $ticket)
        @php
            $statusColor = match($ticket->status) {
                'new'         => 'bg-orange-100 text-orange-600',
                'in_progress' => 'bg-blue-100 text-blue-600',
                'resolved'    => 'bg-green-100 text-green-700',
            };
            $statusLabel = match($ticket->status) {
                'new'         => 'Новое',
                'in_progress' => 'В работе',
                'resolved'    => 'Решено',
            };
        @endphp
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
            </div>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $ticket->message }}</p>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
