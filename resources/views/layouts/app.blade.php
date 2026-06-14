<!DOCTYPE html>
<html lang="ru" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FitTrack') }} - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4ff', 100: '#e0e9ff', 500: '#1e3a5f',
                            600: '#162d4a', 700: '#0f2035', 800: '#0a1628', 900: '#050d18',
                        },
                        orange: { 400: '#fb923c', 500: '#f97316', 600: '#ea580c' }
                    }
                }
            }
        }
    </script>

    {{-- Apply dark mode BEFORE render to avoid flash --}}
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/favicon.png?v=2">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')

    <style>
        /* ===== DARK MODE ===== */

        /* Background with subtle gradient */
        .dark body {
            background-color: #0a0f1a !important;
            background-image: radial-gradient(ellipse at 20% 0%, rgba(249,115,22,0.04) 0%, transparent 50%),
                              radial-gradient(ellipse at 80% 100%, rgba(59,130,246,0.04) 0%, transparent 50%);
            color: #e2e8f0;
        }
        .dark .bg-gray-50 { background-color: #0a0f1a !important; }

        /* Cards — глубокий тёмный с еле заметной границей */
        .dark .bg-white {
            background-color: #111827 !important;
            border: 1px solid rgba(255,255,255,0.06) !important;
        }
        .dark .bg-gray-100 { background-color: #1a2233 !important; }

        /* Тени — мягкое свечение */
        .dark .shadow, .dark .shadow-md, .dark .shadow-2xl {
            box-shadow: 0 4px 24px rgba(0,0,0,0.4), 0 1px 4px rgba(0,0,0,0.3) !important;
        }

        /* Текст */
        .dark .text-gray-400 { color: #6b7fa3 !important; }
        .dark .text-gray-500 { color: #7a8fad !important; }
        .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .text-gray-700 { color: #b8c7de !important; }

        /* Цвет основных заголовков (были #0f2035) */
        .dark [style*="color: #0f2035"] { color: #e2e8f0 !important; }
        .dark [style*="color:#0f2035"]  { color: #e2e8f0 !important; }

        /* Границы */
        .dark .border-gray-100 { border-color: rgba(255,255,255,0.06) !important; }
        .dark .border-gray-200 { border-color: rgba(255,255,255,0.08) !important; }
        .dark .border-gray-300 { border-color: rgba(255,255,255,0.10) !important; }
        .dark .divide-gray-50 > * + * { border-color: rgba(255,255,255,0.05) !important; }

        /* Инпуты */
        .dark input[type=text], .dark input[type=number], .dark input[type=date],
        .dark input[type=time], .dark input[type=email], .dark input[type=password],
        .dark select, .dark textarea {
            background-color: #1a2233 !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #e2e8f0 !important;
        }
        .dark input::placeholder { color: #4a5c7a !important; }

        /* Hover строки */
        .dark .hover\:bg-gray-50:hover { background-color: #1a2233 !important; }

        /* Секция "Сегодня" — тёплый оранжевый оттенок */
        .dark .bg-orange-50 { background: linear-gradient(135deg, #1a1508, #1f1200) !important; }
        .dark .border-orange-100 { border-color: rgba(249,115,22,0.15) !important; }

        /* Секция "Завтра" — холодный синий */
        .dark .bg-blue-50 { background: linear-gradient(135deg, #0a1020, #080e1c) !important; }
        .dark .border-blue-100 { border-color: rgba(59,130,246,0.15) !important; }

        /* Зелёные строки (completed) */
        .dark .bg-green-50 { background-color: #0d1f10 !important; }

        /* Остальные цветные bg */
        .dark .bg-yellow-50 { background-color: #1a1505 !important; }
        .dark .bg-red-50    { background-color: #1f0a0a !important; }
        .dark .bg-purple-50 { background-color: #130a1f !important; }
        .dark .bg-amber-50  { background-color: #1a1205 !important; }

        /* Иконки-пузыри в stat cards */
        .dark .bg-green-100  { background-color: #0d2010 !important; }
        .dark .bg-blue-100   { background-color: #081525 !important; }
        .dark .bg-yellow-100 { background-color: #1a1405 !important; }
        .dark .bg-red-100    { background-color: #1f0808 !important; }
        .dark .bg-purple-100 { background-color: #120820 !important; }

        /* Progress bar bg */
        .dark .bg-gray-200 { background-color: #1e2a3a !important; }

        /* Кнопки с border */
        .dark .border-gray-200.text-gray-400 {
            border-color: rgba(255,255,255,0.10) !important;
            color: #6b7fa3 !important;
        }
        .dark .border-gray-200.text-gray-400:hover {
            border-color: rgba(249,115,22,0.5) !important;
            color: #f97316 !important;
            background-color: rgba(249,115,22,0.05) !important;
        }
        .dark .border-gray-300 { border-color: rgba(255,255,255,0.10) !important; }

        /* Orange блок "лучший клиент" */
        .dark [style*="background-color: #fff7ed"],
        .dark [style*="background:#fff7ed"],
        .dark .from-yellow-50, .dark .to-orange-50 {
            background: linear-gradient(135deg, #1a1205, #1f1500) !important;
        }

        /* Flash messages */
        .dark .bg-green-100.border-green-400 {
            background-color: #0d2010 !important;
            border-color: rgba(34,197,94,0.2) !important;
            color: #4ade80 !important;
        }

        /* Mobile nav */
        .dark #mobile-nav { background-color: #07090f !important; border-color: rgba(255,255,255,0.05) !important; }

        /* Убираем белые bg у inline-стилей stat cards */
        .dark [style*="background-color: #e0e9ff"] { background-color: #1a2a4a !important; color: #93c5fd !important; }

        /* Плавный переход */
        *, *::before, *::after {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.15s ease, box-shadow 0.25s ease;
        }
    </style>
    <style>
        /* Скрыть иконку часов в time-инпутах попапа редактирования */
        input[type=time]::-webkit-calendar-picker-indicator { opacity: 0.4; cursor: pointer; }
        /* Моноширинные цифры для времени */
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Navigation -->
<nav class="shadow-lg" style="background-color: #0f2035;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <i class="fas fa-dumbbell text-xl" style="color: #f97316;"></i>
                    <span class="text-white font-bold text-xl">FitTrack</span>
                </a>
                <div class="hidden md:flex space-x-4">
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'text-white bg-blue-900' : 'text-gray-300 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-1"></i> {{ __('app.nav_monitor') }}
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('clients.*') ? 'text-white bg-blue-900' : 'text-gray-300 hover:text-white' }}">
                        <i class="fas fa-users mr-1"></i> {{ __('app.nav_clients') }}
                    </a>
                    <a href="{{ route('statistics') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('statistics') ? 'text-white bg-blue-900' : 'text-gray-300 hover:text-white' }}">
                        <i class="fas fa-chart-bar mr-1"></i> {{ __('app.nav_statistics') }}
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-1 sm:gap-3">
                {{-- Language switcher --}}
                <div class="flex items-center gap-0.5 text-xs font-semibold" style="position:relative;z-index:100;">
                    <button type="button" onclick="window.location.href='{{ route('lang.switch', 'ru') }}'"
                       class="px-2 py-1.5 rounded-md cursor-pointer border-0"
                       style="{{ app()->getLocale() === 'ru' ? 'background-color:#f97316;color:white;' : 'background:transparent;color:#9ca3af;' }}">
                        RU
                    </button>
                    <button type="button" onclick="window.location.href='{{ route('lang.switch', 'kk') }}'"
                       class="px-2 py-1.5 rounded-md cursor-pointer border-0"
                       style="{{ app()->getLocale() === 'kk' ? 'background-color:#f97316;color:white;' : 'background:transparent;color:#9ca3af;' }}">
                        KZ
                    </button>
                </div>

                {{-- Dark mode toggle --}}
                <button id="theme-toggle" onclick="toggleTheme()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition"
                    title="Переключить тему">
                    <i id="theme-icon" class="fas fa-moon text-sm"></i>
                </button>

                {{-- Админка — только для владельца --}}
                @if(auth()->user()->trial_ends_at === null)
                <a href="{{ route('admin.users') }}" class="text-gray-300 text-sm hidden md:flex items-center gap-1.5 hover:text-orange-400 transition">
                    <i class="fas fa-shield-alt"></i>
                    <span>Админ</span>
                </a>
                <a href="{{ route('admin.tickets') }}" class="text-gray-300 text-sm hidden md:flex items-center gap-1.5 hover:text-orange-400 transition">
                    <i class="fas fa-headset"></i>
                    <span>Обращения</span>
                    @php $newTickets = \App\Models\SupportTicket::where('status','new')->count(); @endphp
                    @if($newTickets > 0)
                    <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $newTickets }}</span>
                    @endif
                </a>
                @endif

                {{-- Профиль — только на десктопе --}}
                <a href="{{ route('profile.edit') }}" class="text-gray-300 text-sm hidden md:flex items-center gap-1.5 hover:text-white transition">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->user()->name }}
                </a>

                {{-- Выйти: иконка на мобиле, текст на десктопе --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white text-sm px-2 py-2 rounded-md hover:bg-blue-900 transition flex items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden md:inline">{{ __('app.nav_logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile nav -->
<div id="mobile-nav" class="md:hidden fixed bottom-0 left-0 right-0 z-50 border-t border-blue-900 safe-area-bottom" style="background-color: #0a1628;">
    <div class="flex">
        <a href="{{ route('dashboard') }}" class="flex-1 text-center py-3 text-sm {{ request()->routeIs('dashboard') ? 'text-orange-400' : 'text-gray-300' }}" style="{{ request()->routeIs('dashboard') ? 'color: #fb923c;' : '' }}">
            <i class="fas fa-tachometer-alt block text-lg"></i>
            {{ __('app.nav_monitor') }}
        </a>
        <a href="{{ route('clients.index') }}" class="flex-1 text-center py-3 text-sm {{ request()->routeIs('clients.*') ? 'text-orange-400' : 'text-gray-300' }}" style="{{ request()->routeIs('clients.*') ? 'color: #fb923c;' : '' }}">
            <i class="fas fa-users block text-lg"></i>
            {{ __('app.nav_clients') }}
        </a>
        <a href="{{ route('statistics') }}" class="flex-1 text-center py-3 text-sm {{ request()->routeIs('statistics') ? 'text-orange-400' : 'text-gray-300' }}" style="{{ request()->routeIs('statistics') ? 'color: #fb923c;' : '' }}">
            <i class="fas fa-chart-bar block text-lg"></i>
            {{ __('app.nav_statistics') }}
        </a>
        <a href="{{ route('profile.edit') }}" class="flex-1 text-center py-3 text-sm {{ request()->routeIs('profile.*') ? 'text-orange-400' : 'text-gray-300' }}" style="{{ request()->routeIs('profile.*') ? 'color: #fb923c;' : '' }}">
            <i class="fas fa-user-circle block text-lg"></i>
            {{ __('app.nav_profile') }}
        </a>
    </div>
</div>

<!-- Trial banner -->
@auth
@if(auth()->user()->trial_ends_at)
@php
    $daysLeft = max(0, (int) now()->diffInDays(auth()->user()->trial_ends_at, false));
@endphp
<div style="background-color: {{ $daysLeft <= 3 ? '#dc2626' : '#f97316' }};" class="text-white text-center py-2 px-4 text-sm font-medium">
    <i class="fas fa-clock mr-1"></i>
    @if($daysLeft > 0)
        Пробный период: осталось <strong>{{ $daysLeft }} {{ $daysLeft === 1 ? 'день' : ($daysLeft < 5 ? 'дня' : 'дней') }}</strong>
    @else
        Пробный период завершён
    @endif
</div>
@endif
@endauth

<!-- Flash messages -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 text-xl leading-none">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif
    @if(session('status'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-info-circle mr-2"></i>{{ session('status') }}
        </div>
    @endif
</div>

<!-- Main content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-6">
    @yield('content')
</main>

<!-- Time picker confirm modal -->
<div id="gtp-confirm-modal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 mx-4">
        <div class="text-center mb-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:#fff7ed;">
                <i class="fas fa-clock text-xl" style="color:#f97316;"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Изменить время</h3>
            <p class="text-gray-500 text-sm mt-1">Новое время: <strong id="gtp-confirm-time" style="color:#f97316;"></strong></p>
        </div>
        <div class="space-y-2">
            <button id="gtp-confirm-all" class="w-full py-3 rounded-xl font-semibold text-white transition" style="background:#f97316;">
                Изменить предстоящие тренировки
            </button>
            <button id="gtp-confirm-single" class="w-full py-3 rounded-xl font-semibold text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Только это занятие
            </button>
            <button onclick="document.getElementById('gtp-confirm-modal').classList.add('hidden')" class="w-full py-2 text-sm text-gray-400 hover:text-gray-600 transition">
                Отмена
            </button>
        </div>
    </div>
</div>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        icon.className = 'fas fa-moon text-sm';
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        icon.className = 'fas fa-sun text-sm text-yellow-300';
    }
}

// ============================================================
// GLOBAL TIME PICKER
// Usage: openTimePicker(btn, '/sessions/ID', 'scheduled', '19:30')
// ============================================================
let _gTimePicker = null, _gpH = 9, _gpM = 0;

function openTimePicker(btn, url, status, currentTime) {
    if (_gTimePicker) { _gTimePicker.remove(); _gTimePicker = null; }

    _gpH = currentTime ? parseInt(currentTime.split(':')[0]) : 9;
    _gpM = currentTime ? parseInt(currentTime.split(':')[1]) : 0;

    const hours   = Array.from({length:24}, (_,i) => i);
    const minutes = [0,5,10,15,20,25,30,35,40,45,50,55];
    const token   = document.querySelector('meta[name=csrf-token]').content;

    const colStyle = 'flex:1;overflow-y:auto;text-align:center;padding:4px 8px;';
    const itemStyle = (sel) => `padding:8px 0;font-size:17px;font-weight:${sel?'700':'400'};color:${sel?'#f97316':'#374151'};cursor:pointer;border-radius:6px;background:${sel?'#fff7ed':'transparent'};`;

    const popup = document.createElement('div');
    popup.id = '_gTimePicker';
    popup.style.cssText = 'position:fixed;z-index:2000;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);overflow:hidden;width:200px;';

    popup.innerHTML = `
        <div style="background:#0f2035;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#fff;font-size:13px;font-weight:600;">Время занятия</span>
            <span id="gtp-preview" style="color:#fb923c;font-size:15px;font-weight:700;">
                ${String(_gpH).padStart(2,'0')}:${String(_gpM).padStart(2,'0')}
            </span>
        </div>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;">
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;border-right:1px solid #f3f4f6;">Часы</div>
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;">Минуты</div>
        </div>
        <div style="display:flex;height:210px;">
            <div id="gtp-hours" style="${colStyle}border-right:1px solid #f3f4f6;">
                ${hours.map(v=>`<div data-v="${v}" onclick="gtpSelect('h',${v},this)" style="${itemStyle(v===_gpH)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
            <div id="gtp-mins" style="${colStyle}">
                ${minutes.map(v=>`<div data-v="${v}" onclick="gtpSelect('m',${v},this)" style="${itemStyle(v===_gpM)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
        </div>
        <div style="padding:10px 12px;">
            <form method="POST" action="${url}" id="gtp-form">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="_token" value="${token}">
                <input type="hidden" name="status" value="${status}">
                <input type="hidden" name="scheduled_time" id="gtp-value"
                    value="${String(_gpH).padStart(2,'0')}:${String(_gpM).padStart(2,'0')}">
                <button type="button" onclick="gtpConfirm('${url}')" style="width:100%;background:#f97316;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    ✓ OK
                </button>
            </form>
        </div>`;

    document.body.appendChild(popup);
    _gTimePicker = popup;

    // Position near the button
    const rect = btn.getBoundingClientRect();
    const pw = popup.offsetWidth, ph = popup.offsetHeight;
    let top = rect.bottom + 6;
    if (top + ph > window.innerHeight - 10) top = rect.top - ph - 6;
    let left = rect.left;
    if (left + pw > window.innerWidth - 10) left = window.innerWidth - pw - 10;
    if (left < 8) left = 8;
    popup.style.top = top + 'px';
    popup.style.left = left + 'px';

    // Scroll to selected
    setTimeout(() => {
        const hEl = popup.querySelector(`#gtp-hours [data-v="${_gpH}"]`);
        const mEl = popup.querySelector(`#gtp-mins [data-v="${_gpM}"]`);
        if (hEl) hEl.scrollIntoView({block:'center'});
        if (mEl) mEl.scrollIntoView({block:'center'});
    }, 10);

    // Close on outside click
    setTimeout(() => {
        document.addEventListener('click', function _gtpHandler(e) {
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.remove(); _gTimePicker = null;
                document.removeEventListener('click', _gtpHandler);
            }
        });
    }, 100);
}

function gtpSelect(type, val, el) {
    const colId = type === 'h' ? 'gtp-hours' : 'gtp-mins';
    document.querySelectorAll('#' + colId + ' div').forEach(d => {
        d.style.fontWeight = '400'; d.style.color = '#374151'; d.style.background = 'transparent';
    });
    el.style.fontWeight = '700'; el.style.color = '#f97316'; el.style.background = '#fff7ed';
    if (type === 'h') _gpH = val; else _gpM = val;
    const t = String(_gpH).padStart(2,'0') + ':' + String(_gpM).padStart(2,'0');
    document.getElementById('gtp-value').value = t;
    document.getElementById('gtp-preview').textContent = t;
}
// ============================================================

// Time picker confirm popup
function gtpConfirm(url) {
    const time = document.getElementById('gtp-value').value;
    const status = document.querySelector('#gtp-form [name=status]').value;
    const token = document.querySelector('#gtp-form [name=_token]').value;

    // Show confirm popup
    const modal = document.getElementById('gtp-confirm-modal');
    document.getElementById('gtp-confirm-time').textContent = time;
    document.getElementById('gtp-confirm-single').onclick = function() {
        // Submit just this session
        const form = document.getElementById('gtp-form');
        form.submit();
        modal.classList.add('hidden');
    };
    document.getElementById('gtp-confirm-all').onclick = function() {
        // Submit all future sessions in this package
        const params = new URLSearchParams(window.location.search);
        const filter = params.get('filter') || 'today';
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = url + '/update-package-time';
        f.innerHTML = `<input type="hidden" name="_token" value="${token}">
                       <input type="hidden" name="scheduled_time" value="${time}">
                       <input type="hidden" name="redirect_filter" value="${filter}">`;
        document.body.appendChild(f);
        f.submit();
        modal.classList.add('hidden');
    };
    modal.classList.remove('hidden');
}

// Set correct icon on load
document.addEventListener('DOMContentLoaded', function() {
    const icon = document.getElementById('theme-icon');
    if (document.documentElement.classList.contains('dark')) {
        icon.className = 'fas fa-sun text-sm text-yellow-300';
    }
});
</script>
</body>
</html>
