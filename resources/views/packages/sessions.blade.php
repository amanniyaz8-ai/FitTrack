@extends('layouts.app')
@section('title', 'Занятия пакета')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('clients.show', $package->client) }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color: #0f2035;">Занятия</h1>
            <p class="text-gray-500 text-sm">
                <a href="{{ route('clients.show', $package->client) }}" class="hover:underline">{{ $package->client->full_name }}</a>
                — {{ $package->total_sessions }} тренировок
                @if($package->is_paid)
                    <span class="text-green-600 ml-2"><i class="fas fa-check-circle"></i> Оплачен</span>
                @else
                    <span class="text-red-500 ml-2"><i class="fas fa-times-circle"></i> Не оплачен</span>
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Stats bar -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
        <p class="text-xl md:text-2xl font-bold text-green-600">{{ $completedCount }}</p>
        <p class="text-gray-400 text-xs md:text-sm">Выполнено</p>
    </div>
    <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
        <p class="text-xl md:text-2xl font-bold text-red-500">{{ $missedCount }}</p>
        <p class="text-gray-400 text-xs md:text-sm">Пропущено</p>
    </div>
    <div class="bg-white rounded-xl shadow p-3 md:p-4 text-center">
        <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $remaining }}</p>
        <p class="text-gray-400 text-xs md:text-sm">Осталось</p>
    </div>
</div>

<!-- Progress -->
<div class="bg-white rounded-xl shadow p-4 mb-6">
    @php
        $progressPercent = $package->total_sessions > 0 ? (int) round(($completedCount / $package->total_sessions) * 100) : 0;
    @endphp
    <div class="flex justify-between text-sm text-gray-500 mb-2">
        <span>Прогресс</span>
        <span>{{ $progressPercent }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div class="h-3 rounded-full transition-all" style="width: {{ $progressPercent }}%; background: linear-gradient(to right, #fb923c, #ea580c);"></div>
    </div>
</div>

<!-- Add session manually -->
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <h3 class="font-medium mb-3" style="color: #0f2035;"><i class="fas fa-plus mr-2" style="color: #fb923c;"></i>Добавить занятие вручную</h3>
    <form method="POST" action="{{ route('packages.addSession', $package) }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-center">
        @csrf
        <input type="date" name="scheduled_date" value="{{ date('Y-m-d') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 w-full sm:flex-1">
        <input type="time" name="scheduled_time" value="17:00"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 w-full sm:w-32">
        <button type="submit" class="text-white px-4 py-2 rounded-lg text-sm transition w-full sm:w-auto" style="background-color: #0f2035;" onmouseover="this.style.backgroundColor='#162d4a'" onmouseout="this.style.backgroundColor='#0f2035'">
            Добавить
        </button>
    </form>
</div>

<!-- Sessions list -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between" style="background-color: #0f2035;">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="select-all" title="Выбрать все"
                class="w-4 h-4 rounded cursor-pointer" style="accent-color:#f97316;">
            <h2 class="text-white font-medium">Список занятий ({{ $sessions->count() }})</h2>
        </div>
        <span id="selected-count" class="text-xs text-gray-400 hidden"></span>
    </div>

    @if($sessions->isEmpty())
    <div class="text-center py-10 text-gray-400">
        <i class="fas fa-calendar-times text-4xl mb-3"></i>
        <p>Занятий нет</p>
    </div>
    @else
    <div class="divide-y divide-gray-50" id="sessions-list">
        @foreach($sessions as $i => $session)
        @php
            $ruDays = ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'];
            $dayName = $ruDays[$session->scheduled_date->dayOfWeek] ?? '';
        @endphp
        <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50 session-row transition" id="session-{{ $session->id }}"
             data-id="{{ $session->id }}">
            <div class="flex items-center gap-3 flex-1">
                <input type="checkbox" class="session-checkbox w-4 h-4 rounded cursor-pointer shrink-0"
                       value="{{ $session->id }}" style="accent-color:#f97316;">
                <span class="text-gray-400 text-sm w-5 text-right shrink-0">{{ $i + 1 }}</span>
                {{-- Time badge --}}
                <button type="button"
                    onclick="openTimePicker(this, '{{ route('sessions.update', $session) }}', '{{ $session->status }}', '{{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '' }}')"
                    class="w-14 text-center group cursor-pointer shrink-0"
                    title="Указать время">
                    <p class="text-sm font-bold tabular-nums group-hover:opacity-70 transition"
                       style="{{ $session->scheduled_time ? 'color:#f97316;' : 'color:#94a3b8;' }}">
                        {{ $session->scheduled_time ? \Carbon\Carbon::parse($session->scheduled_time)->format('H:i') : '—:—' }}
                    </p>
                    <p class="text-xs text-gray-400"><i class="fas fa-pen" style="font-size:9px;"></i></p>
                </button>
                <div>
                    <p class="font-medium" style="color: #0f2035;">
                        {{ $session->scheduled_date->format('d.m.Y') }}
                        <span class="text-gray-400 text-xs font-normal ml-1">{{ $dayName }}</span>
                    </p>
                    @if($session->notes)
                        <p class="text-gray-400 text-xs mt-0.5">{{ $session->notes }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Status update form -->
                <form method="POST" action="{{ route('sessions.update', $session) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                        onchange="handleStatusChange(this, {{ $session->id }}, '{{ $session->scheduled_date->format('d.m.Y') }}')"
                        class="border rounded-lg px-1.5 md:px-3 py-1.5 text-xs md:text-sm focus:outline-none cursor-pointer max-w-[120px] md:max-w-none
                        {{ $session->status === 'completed' ? 'bg-green-50 border-green-300 text-green-700' :
                           ($session->status === 'missed'    ? 'bg-red-50 border-red-300 text-red-600' :
                           ($session->status === 'cancelled' ? 'bg-purple-50 border-purple-300 text-purple-700' :
                           'bg-blue-50 border-blue-300 text-blue-700')) }}">
                        <option value="scheduled"  {{ $session->status === 'scheduled'  ? 'selected' : '' }}>Запланировано</option>
                        <option value="completed"  {{ $session->status === 'completed'  ? 'selected' : '' }}>Отходил</option>
                        <option value="missed"     {{ $session->status === 'missed'     ? 'selected' : '' }}>Пропустил</option>
                        <option value="cancelled"  {{ $session->status === 'cancelled'  ? 'selected' : '' }}>Отменил</option>
                    </select>
                </form>

                <!-- Delete button -->
                <form method="POST" action="{{ route('sessions.destroy', $session) }}"
                      onsubmit="return confirm('Удалить занятие {{ $session->scheduled_date->format('d.m.Y') }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition"
                        title="Удалить занятие">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
{{-- Bulk action floating bar --}}
<div id="bulk-bar" class="fixed bottom-4 left-2 right-2 md:left-1/2 md:right-auto md:-translate-x-1/2 z-50 hidden">
    <div class="flex flex-col sm:flex-row items-center gap-2 px-4 py-3 rounded-2xl shadow-2xl text-sm" style="background:#0f2035;">
        <span class="text-white font-medium">
            <i class="fas fa-check-square mr-1" style="color:#fb923c;"></i>
            <span id="bulk-count">0</span> выбрано
        </span>
        <div class="flex gap-1.5 flex-wrap justify-center">
            <button onclick="bulkSetStatus('completed')"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-green-500 text-white hover:bg-green-600 transition">
                <i class="fas fa-check mr-1"></i>Отходил
            </button>
            <button onclick="bulkSetStatus('missed')"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-500 text-white hover:bg-red-600 transition">
                <i class="fas fa-user-times mr-1"></i>Пропустил
            </button>
            <button onclick="bulkSetStatus('cancelled')"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-purple-500 text-white hover:bg-purple-600 transition">
                <i class="fas fa-calendar-times mr-1"></i>Отменил
            </button>
            <button onclick="bulkSetStatus('scheduled')"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-blue-500 text-white hover:bg-blue-600 transition">
                <i class="fas fa-calendar mr-1"></i>План
            </button>
            <button onclick="clearSelection()"
                class="px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-500 text-gray-300 hover:text-white hover:border-white transition">
                ✕
            </button>
        </div>
    </div>
</div>

{{-- Hidden form for bulk update --}}
<form id="bulk-form" method="POST" action="{{ route('sessions.bulkUpdate') }}" class="hidden">
    @csrf
    <input type="hidden" name="status" id="bulk-status">
    <div id="bulk-ids"></div>
</form>

{{-- Reschedule modal --}}
<div id="reschedule-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4">
        <h3 class="text-lg font-bold mb-1" style="color:#0f2035;">
            <i class="fas fa-calendar-alt text-orange-400 mr-2"></i>Перенос занятия
        </h3>
        <p class="text-sm text-gray-400 mb-4" id="modal-subtitle">Клиент отменил тренировку</p>

        <form id="reschedule-form" method="POST">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Перенести на дату</label>
                    <input type="date" name="new_date" id="reschedule-date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Время (необязательно)</label>
                    <input type="time" name="new_time" id="reschedule-time"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="submit"
                    class="flex-1 text-white py-2 rounded-lg font-medium transition"
                    style="background-color:#f97316"
                    onmouseover="this.style.backgroundColor='#ea580c'"
                    onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-calendar-check mr-1"></i>Перенести
                </button>
                <button type="button" onclick="closeRescheduleModal()"
                    class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let pendingSelect = null;
let pendingOriginalValue = null;

function handleStatusChange(select, sessionId, dateLabel) {
    if (select.value === 'cancelled') {
        // Remember select and its previous value before showing modal
        pendingSelect = select;
        pendingOriginalValue = Array.from(select.options).find(o => o.value !== 'cancelled' && o.defaultSelected)?.value
            || select.dataset.original || 'scheduled';
        select.dataset.original = select.value;

        // Set form action
        document.getElementById('reschedule-form').action = '/sessions/' + sessionId + '/reschedule';
        document.getElementById('modal-subtitle').textContent = 'Клиент отменил занятие от ' + dateLabel;

        // Set default reschedule date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('reschedule-date').value = tomorrow.toISOString().split('T')[0];

        document.getElementById('reschedule-modal').classList.remove('hidden');
    } else {
        select.form.submit();
    }
}

function closeRescheduleModal() {
    document.getElementById('reschedule-modal').classList.add('hidden');
    // Revert select to previous value
    if (pendingSelect) {
        // Find the previously selected option
        Array.from(pendingSelect.options).forEach(o => { o.selected = (o.value === (pendingSelect.dataset.prev || 'scheduled')); });
    }
}

// ---- Bulk selection ----
function getChecked() {
    return Array.from(document.querySelectorAll('.session-checkbox:checked')).map(c => c.value);
}

function updateBulkBar() {
    const ids = getChecked();
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = ids.length;
    if (ids.length > 0) {
        bar.classList.remove('hidden');
    } else {
        bar.classList.add('hidden');
    }
    // Highlight selected rows
    document.querySelectorAll('.session-checkbox').forEach(cb => {
        cb.closest('.session-row').style.background = cb.checked ? '#fff7ed' : '';
    });
}

function clearSelection() {
    document.querySelectorAll('.session-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    updateBulkBar();
}

function bulkSetStatus(status) {
    const ids = getChecked();
    if (ids.length === 0) return;

    const labels = { completed: 'Отходил', missed: 'Пропустил', cancelled: 'Отменил', scheduled: 'Запланировано' };
    if (!confirm('Установить статус "' + labels[status] + '" для ' + ids.length + ' занятий?')) return;

    document.getElementById('bulk-status').value = status;
    const container = document.getElementById('bulk-ids');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'session_ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('bulk-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.session-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });
    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.session-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });
});

// Save original value on page load
document.querySelectorAll('select[name=status]').forEach(sel => {
    sel.dataset.prev = sel.value;
    sel.addEventListener('change', function() {
        if (this.value !== 'cancelled') this.dataset.prev = this.value;
    });
});

// Close modal on backdrop click
document.getElementById('reschedule-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRescheduleModal();
});

</script>
@endsection
