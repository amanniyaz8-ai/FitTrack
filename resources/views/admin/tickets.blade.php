@extends('layouts.app')
@section('title', 'Обращения в поддержку')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold" style="color:#0f2035;">Обращения в поддержку</h1>
        <p class="text-sm text-gray-400 mt-0.5">Заявки от пользователей сайта</p>
    </div>
    <div class="flex gap-2">
        <span class="text-sm px-3 py-1 rounded-full bg-red-50 text-red-600 font-semibold">
            {{ $tickets->where('status','new')->count() }} новых
        </span>
    </div>
</div>

@if($tickets->isEmpty())
<div class="bg-white rounded-xl shadow p-12 text-center">
    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
    <p class="text-gray-400">Обращений пока нет</p>
</div>
@else
<div class="space-y-3">
    @foreach($tickets as $ticket)
    <div class="bg-white rounded-xl shadow p-5 border-l-4 {{ $ticket->status === 'new' ? 'border-red-400' : ($ticket->status === 'in_progress' ? 'border-yellow-400' : 'border-green-400') }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="font-semibold" style="color:#0f2035;">{{ $ticket->name }}</span>
                    @if($ticket->status === 'new')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-semibold">Новое</span>
                    @elseif($ticket->status === 'in_progress')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-600 font-semibold">В работе</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-semibold">Решено</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="flex gap-3 text-xs text-gray-500 mb-3 flex-wrap">
                    @if($ticket->phone)
                        <span><i class="fas fa-phone mr-1"></i>{{ $ticket->phone }}</span>
                    @endif
                    @if($ticket->email)
                        <span><i class="fas fa-envelope mr-1"></i>{{ $ticket->email }}</span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $ticket->message }}</p>
            </div>
            <div class="flex flex-col gap-2 shrink-0">
                @if($ticket->status !== 'in_progress')
                <form method="POST" action="/admin/tickets/{{ $ticket->id }}/status">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button class="text-xs px-3 py-1.5 rounded-lg border border-yellow-300 text-yellow-600 hover:bg-yellow-50 transition whitespace-nowrap">
                        В работу
                    </button>
                </form>
                @endif
                @if($ticket->status !== 'resolved')
                <form method="POST" action="/admin/tickets/{{ $ticket->id }}/status">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="resolved">
                    <button class="text-xs px-3 py-1.5 rounded-lg border border-green-300 text-green-600 hover:bg-green-50 transition whitespace-nowrap">
                        Решено
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
