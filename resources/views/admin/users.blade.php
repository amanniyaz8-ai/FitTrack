<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Админ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-dumbbell text-2xl" style="color:#f97316;"></i>
            <div>
                <h1 class="text-xl font-bold" style="color:#0f2035;">FitTrack Admin</h1>
                <p class="text-xs text-gray-400">Управление подписками</p>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-orange-500 transition">
            <i class="fas fa-arrow-left mr-1"></i>В приложение
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold" style="color:#0f2035;">{{ $users->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Всего тренеров</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold text-green-600">{{ $users->filter(fn($u) => $u->subscription_ends_at && $u->subscription_ends_at->isFuture())->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Активных подписок</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold text-orange-500">{{ $users->filter(fn($u) => $u->trial_ends_at && $u->trial_ends_at->isFuture())->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">На пробном периоде</p>
        </div>
    </div>

    {{-- Users table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b" style="background:#0f2035;">
            <h2 class="text-white font-semibold"><i class="fas fa-users mr-2" style="color:#f97316;"></i>Тренеры</h2>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($users as $user)
            <div class="px-6 py-4">
                <div class="flex flex-wrap items-center gap-4">

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-400 truncate">{{ $user->email }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Регистрация: {{ $user->created_at->format('d.m.Y') }}</p>
                    </div>

                    {{-- Status --}}
                    <div class="text-center shrink-0">
                        @if($user->subscription_ends_at && $user->subscription_ends_at->isFuture())
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <i class="fas fa-crown mr-1"></i>Подписка до {{ $user->subscription_ends_at->format('d.m.Y') }}
                            </span>
                        @elseif($user->trial_ends_at && $user->trial_ends_at->isFuture())
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-600">
                                <i class="fas fa-clock mr-1"></i>Пробный до {{ $user->trial_ends_at->format('d.m.Y') }}
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                                <i class="fas fa-lock mr-1"></i>Нет доступа
                            </span>
                        @endif
                    </div>

                    {{-- Grant access buttons --}}
                    <div class="flex gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.grant', $user) }}">
                            @csrf
                            <input type="hidden" name="months" value="1">
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-white transition" style="background:#f97316;" onmouseover="this.style.background='#ea6c10'" onmouseout="this.style.background='#f97316'">
                                +1 мес
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.grant', $user) }}">
                            @csrf
                            <input type="hidden" name="months" value="6">
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-white transition" style="background:#f97316;" onmouseover="this.style.background='#ea6c10'" onmouseout="this.style.background='#f97316'">
                                +6 мес
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.grant', $user) }}">
                            @csrf
                            <input type="hidden" name="months" value="12">
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-white transition" style="background:#0f2035;" onmouseover="this.style.background='#1a3a5c'" onmouseout="this.style.background='#0f2035'">
                                +1 год
                            </button>
                        </form>
                        @if($user->subscription_ends_at && $user->subscription_ends_at->isFuture())
                        <form method="POST" action="{{ route('admin.revoke', $user) }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-white bg-red-500 hover:bg-red-600 transition"
                                onclick="return confirm('Отозвать доступ у {{ addslashes($user->name) }}?')">
                                Отозвать
                            </button>
                        </form>
                        @endif
                    </div>

                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-users text-3xl mb-2"></i>
                <p>Нет зарегистрированных тренеров</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
</body>
</html>
