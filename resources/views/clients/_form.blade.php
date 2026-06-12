@php
    $days = [
        'Mon' => 'Пн', 'Tue' => 'Вт', 'Wed' => 'Ср', 'Thu' => 'Чт',
        'Fri' => 'Пт', 'Sat' => 'Сб', 'Sun' => 'Вс',
    ];
    $selectedDays = old('training_days', $client->training_days ?? []);
@endphp

@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <ul class="text-red-600 text-sm space-y-1">
        @foreach($errors->all() as $error)
            <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">ФИО <span class="text-red-500">*</span></label>
        <input type="text" name="full_name" value="{{ old('full_name', $client->full_name ?? '') }}"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 {{ $errors->has('full_name') ? 'border-red-400' : 'border-gray-300' }}"
            style="--tw-ring-color: #fb923c;"
            placeholder="Иванова Анна Сергеевна">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
        <input type="text" name="phone" value="{{ old('phone', $client->phone ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2"
            placeholder="+7 777 123 4567">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Цель тренировок</label>
        <textarea name="goal" rows="3"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2"
            placeholder="Например: похудеть на 10 кг, набрать мышечную массу...">{{ old('goal', $client->goal ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Противопоказания</label>
        <textarea name="contraindications" rows="2"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2"
            placeholder="Укажите если есть противопоказания...">{{ old('contraindications', $client->contraindications ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Дни тренировок <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-2">
            @foreach($days as $value => $label)
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="training_days[]" value="{{ $value }}"
                    {{ in_array($value, $selectedDays) ? 'checked' : '' }}
                    class="sr-only peer">
                <span class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 transition select-none
                    peer-checked:text-white peer-checked:border-orange-500
                    hover:border-orange-400"
                    style=""
                    onclick=""
                    data-value="{{ $value }}">
                    {{ $label }}
                </span>
            </label>
            @endforeach
        </div>
        @error('training_days')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-clock text-orange-400 mr-1"></i> Время тренировок
        </label>
        <input type="time" name="training_time" value="{{ old('training_time', $client->training_time ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
            placeholder="Например: 10:00">
        <p class="text-xs text-gray-400 mt-1">Закрепите время — оно будет отображаться в мониторинге</p>
    </div>
</div>

@if(!isset($client->id))
{{-- Секция первого пакета — только при создании клиента --}}
<div class="mt-6 border-t border-gray-200 pt-6">
    <div class="flex items-center gap-3 mb-4">
        <input type="checkbox" name="create_package" id="create_package" value="1"
            {{ old('create_package') ? 'checked' : '' }}
            class="w-4 h-4 rounded" style="accent-color: #f97316;"
            onchange="document.getElementById('package-fields').classList.toggle('hidden', !this.checked)">
        <label for="create_package" class="text-sm font-semibold text-gray-700 cursor-pointer">
            <i class="fas fa-box text-orange-400 mr-1"></i> Сразу создать пакет (абонемент)
        </label>
    </div>

    <div id="package-fields" class="{{ old('create_package') ? '' : 'hidden' }} space-y-4 bg-orange-50 border border-orange-200 rounded-xl p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Количество тренировок</label>
                <input type="number" name="pkg_total_sessions" id="pkg_total_sessions" value="{{ old('pkg_total_sessions', 10) }}" min="1" max="100"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Стоимость (₸)</label>
                <div class="relative">
                    <input type="number" name="pkg_price" value="{{ old('pkg_price', 60000) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                    <span class="absolute right-3 top-2.5 text-gray-400 text-sm">₸</span>
                </div>
            </div>
        </div>

        {{-- Перенос клиента --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-history text-blue-400 mr-1"></i> Уже отходил тренировок
                <span class="text-gray-400 font-normal">(при переносе существующего клиента)</span>
            </label>
            <input type="number" name="pkg_completed_sessions" id="pkg_completed_sessions" value="{{ old('pkg_completed_sessions', 0) }}" min="0" max="100"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white"
                placeholder="0">
            <p id="completed-hint" class="text-xs text-blue-500 mt-1 hidden"></p>
        </div>
        {{-- Стоимость одной тренировки --}}
        <div class="flex items-center justify-between gap-2 px-3 py-2 bg-white border border-orange-200 rounded-lg text-sm">
            <div class="flex items-center gap-2 min-w-0">
                <i class="fas fa-tag text-orange-400 shrink-0"></i>
                <span class="text-gray-500">Стоимость одной тренировки:</span>
            </div>
            <span id="price-per-session-create" class="font-bold whitespace-nowrap shrink-0" style="color:#f97316;">—</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Дата оплаты</label>
                <input type="date" name="pkg_payment_date" value="{{ old('pkg_payment_date', date('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
            </div>
            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="pkg_is_paid" value="1" {{ old('pkg_is_paid') ? 'checked' : '' }}
                        class="w-4 h-4 rounded" style="accent-color: #f97316;">
                    <span class="text-sm font-medium text-gray-700">Оплачен</span>
                </label>
            </div>
        </div>
    </div>
</div>
@endif

<script>
(function() {
    function recalcCreate() {
        const price    = parseFloat(document.querySelector('[name=pkg_price]')?.value) || 0;
        const sessions = parseInt(document.querySelector('[name=pkg_total_sessions]')?.value) || 1;
        const per = sessions > 0 ? Math.round(price / sessions) : 0;
        const el = document.getElementById('price-per-session-create');
        if (el) el.textContent = per > 0 ? per.toLocaleString('ru-RU') + ' ₸' : '—';
    }
    function updateCompletedHint() {
        const total = parseInt(document.getElementById('pkg_total_sessions')?.value) || 0;
        const completed = parseInt(document.getElementById('pkg_completed_sessions')?.value) || 0;
        const hint = document.getElementById('completed-hint');
        if (!hint) return;
        if (completed > 0) {
            const remaining = total - completed;
            hint.textContent = remaining > 0
                ? `Будет создано ${completed} завершённых + ${remaining} предстоящих занятий`
                : `Все ${total} занятий будут отмечены как завершённые`;
            hint.classList.remove('hidden');
        } else {
            hint.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const priceInput     = document.querySelector('[name=pkg_price]');
        const sessionInput   = document.getElementById('pkg_total_sessions');
        const completedInput = document.getElementById('pkg_completed_sessions');
        if (priceInput)     priceInput.addEventListener('input', recalcCreate);
        if (sessionInput)   sessionInput.addEventListener('input', () => { recalcCreate(); updateCompletedHint(); });
        if (completedInput) completedInput.addEventListener('input', updateCompletedHint);
        recalcCreate();
        updateCompletedHint();
    });
})();
</script>

<style>
    input[type="checkbox"].sr-only:checked + span {
        background-color: #f97316;
        color: white;
        border-color: #f97316;
    }
</style>
