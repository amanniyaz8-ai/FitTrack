@extends('layouts.app')
@section('title', 'Новый клиент')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold" style="color: #0f2035;">Новый клиент</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            @php $client = null; @endphp
            @include('clients._form')
            <div class="mt-6 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Создать клиента
                </button>
                <a href="{{ route('clients.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Отмена</a>
            </div>
        </form>
    </div>
</div>
@endsection
