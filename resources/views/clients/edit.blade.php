@extends('layouts.app')
@section('title', 'Редактирование клиента')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="{{ route('clients.show', $client) }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold" style="color: #0f2035;">Редактирование: {{ $client->full_name }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf
            @method('PATCH')
            @include('clients._form')
            <div class="mt-6 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Сохранить
                </button>
                <a href="{{ route('clients.show', $client) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Отмена</a>
            </div>
        </form>
    </div>

    <!-- Delete -->
    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <h3 class="text-red-700 font-semibold mb-2">Опасная зона</h3>
        <p class="text-red-600 text-sm mb-3">Клиент будет перемещён в архив (soft delete). Все его данные сохранятся.</p>
        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Удалить клиента {{ addslashes($client->full_name) }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-trash mr-2"></i>Удалить клиента
            </button>
        </form>
    </div>
</div>
@endsection
