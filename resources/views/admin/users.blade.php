<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack — Админ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-dumbbell text-2xl" style="color:#f97316;"></i>
            <div>
                <h1 class="text-xl font-bold" style="color:#0f2035;">FitTrack Admin</h1>
                <p class="text-xs text-gray-400">Управление тренерами</p>
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold" style="color:#0f2035;">{{ $users->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Тренеров</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold text-green-600">{{ $users->filter(fn($u) => $u->subscription_ends_at && $u->subscription_ends_at->isFuture())->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Активных подписок</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold text-orange-500">{{ $users->filter(fn($u) => !($u->subscription_ends_at && $u->subscription_ends_at->isFuture()) && $u->trial_ends_at && $u->trial_ends_at->isFuture())->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">На пробном периоде</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $totalClients }}</p>
            <p class="text-xs text-gray-400 mt-1">Всего клиентов</p>
        </div>
    </div>

    {{-- Users --}}
    <div class="space-y-4">
        @forelse($users as $user)
        @php
            $isActive = $user->subscription_ends_at && $user->subscription_ends_at->isFuture();
            $isTrial  = !$isActive && $user->trial_ends_at && $user->trial_ends_at->isFuture();
            $clientCount = $user->clients_count;
            $sessionCount = $user->clients->sum(fn($c) => $c->sessions->where('status','completed')->count());
        @endphp
        <div class="bg-white rounded-xl shadow overflow-hidden">
            {{-- Trainer header --}}
            <div class="px-5 py-4 flex flex-wrap items-center gap-4 border-b border-gray-100">
                {{-- Avatar + name --}}
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                         style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        <p class="text-xs text-gray-300 mt-0.5">Регистрация: {{ $user->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>

                {{-- Stats mini --}}
                <div class="flex gap-4 text-center shrink-0">
                    <div>
                        <p class="text-lg font-bold" style="color:#0f2035;">{{ $clientCount }}</p>
                        <p class="text-xs text-gray-400">клиентов</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-green-600">{{ $sessionCount }}</p>
                        <p class="text-xs text-gray-400">тренировок</p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="shrink-0">
                    @if($isActive)
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <i class="fas fa-crown mr-1"></i>Подписка до {{ $user->subscription_ends_at->format('d.m.Y') }}
                        </span>
                    @elseif($isTrial)
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-600">
                            <i class="fas fa-clock mr-1"></i>Пробный до {{ $user->trial_ends_at->format('d.m.Y') }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                            <i class="fas fa-lock mr-1"></i>Нет доступа
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.grant', $user) }}">
                        @csrf
                        <input type="hidden" name="months" value="1">
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition" style="background:#f97316;" onmouseover="this.style.background='#ea6c10'" onmouseout="this.style.background='#f97316'">+1 мес</button>
                    </form>
                    <form method="POST" action="{{ route('admin.grant', $user) }}">
                        @csrf
                        <input type="hidden" name="months" value="12">
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition" style="background:#0f2035;" onmouseover="this.style.background='#1a3a5c'" onmouseout="this.style.background='#0f2035'">+1 год</button>
                    </form>
                    @if($isActive)
                    <form method="POST" action="{{ route('admin.revoke', $user) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-red-500 hover:bg-red-600 transition"
                            onclick="return confirm('Отозвать доступ у {{ addslashes($user->name) }}?')">Отозвать</button>
                    </form>
                    @endif
                    {{-- Toggle clients --}}
                    @if($clientCount > 0)
                    <button onclick="toggleClients('clients_{{ $user->id }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                        <i class="fas fa-users mr-1"></i>Клиенты
                    </button>
                    @endif
                </div>
            </div>

            {{-- Clients list (hidden by default) --}}
            @if($clientCount > 0)
            <div id="clients_{{ $user->id }}" class="hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Клиенты тренера</p>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($user->clients as $client)
                    @php
                        $completed = $client->sessions->where('status','completed')->count();
                        $missed    = $client->sessions->where('status','missed')->count();
                        $pkgCount  = $client->packages_count;
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-4">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                            {{ mb_substr($client->full_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $client->full_name }}</p>
                            @if($client->phone)
                            <p class="text-xs text-gray-400">{{ $client->phone }}</p>
                            @endif
                        </div>
                        <div class="flex gap-4 text-center text-xs shrink-0">
                            <div>
                                <p class="font-semibold text-green-600">{{ $completed }}</p>
                                <p class="text-gray-400">выполнено</p>
                            </div>
                            <div>
                                <p class="font-semibold text-red-500">{{ $missed }}</p>
                                <p class="text-gray-400">пропущено</p>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-600">{{ $pkgCount }}</p>
                                <p class="text-gray-400">пакетов</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl shadow px-6 py-10 text-center text-gray-400">
            <i class="fas fa-users text-3xl mb-2"></i>
            <p>Нет зарегистрированных тренеров</p>
        </div>
        @endforelse
    </div>

</div>

<script>
function toggleClients(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
}
</script>
</body>
</html>
