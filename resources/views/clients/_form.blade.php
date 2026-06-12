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
        <input type="hidden" name="training_time" id="client_training_time_val" value="{{ old('training_time', $client->training_time ?? '') }}">
        <button type="button" id="client_time_btn"
            onclick="openClientTimePicker(this)"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-left flex items-center justify-between hover:border-orange-400 transition focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
            <span id="client_time_label" class="text-sm font-semibold" style="color:{{ old('training_time', $client->training_time ?? '') ? '#f97316' : '#9ca3af' }};">
                {{ old('training_time', $client->training_time ?? '') ?: 'Выбрать время' }}
            </span>
            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
        </button>
        <p class="text-xs text-gray-400 mt-1">Закрепите время — оно будет отображаться в мониторинге</p>
    </div>

<script>
function openClientTimePicker(btn) {
    if (window._gTimePicker) { window._gTimePicker.remove(); window._gTimePicker = null; }

    const current = document.getElementById('client_training_time_val').value;
    let _h = current ? parseInt(current.split(':')[0]) : 9;
    let _m = current ? parseInt(current.split(':')[1]) : 0;

    const hours   = Array.from({length:24}, (_,i) => i);
    const minutes = [0,5,10,15,20,25,30,35,40,45,50,55];
    const colStyle = 'flex:1;overflow-y:auto;text-align:center;padding:4px 8px;';
    const itemStyle = (sel) => `padding:8px 0;font-size:17px;font-weight:${sel?'700':'400'};color:${sel?'#f97316':'#374151'};cursor:pointer;border-radius:6px;background:${sel?'#fff7ed':'transparent'};`;

    const popup = document.createElement('div');
    popup.id = '_gTimePicker';
    popup.style.cssText = 'position:fixed;z-index:2000;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);overflow:hidden;width:200px;';

    popup.innerHTML = `
        <div style="background:#0f2035;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#fff;font-size:13px;font-weight:600;">Время тренировок</span>
            <span id="ctp-preview" style="color:#fb923c;font-size:15px;font-weight:700;">
                ${String(_h).padStart(2,'0')}:${String(_m).padStart(2,'0')}
            </span>
        </div>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;">
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;border-right:1px solid #f3f4f6;">Часы</div>
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;">Минуты</div>
        </div>
        <div style="display:flex;height:210px;">
            <div id="ctp-hours" style="${colStyle}border-right:1px solid #f3f4f6;">
                ${hours.map(v=>`<div data-v="${v}" onclick="ctpSelect('h',${v},this)" style="${itemStyle(v===_h)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
            <div id="ctp-mins" style="${colStyle}">
                ${minutes.map(v=>`<div data-v="${v}" onclick="ctpSelect('m',${v},this)" style="${itemStyle(v===_m)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
        </div>
        <div style="padding:10px 12px;">
            <button type="button" onclick="ctpConfirm()" style="width:100%;background:#f97316;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;">
                ✓ Сохранить
            </button>
        </div>`;

    document.body.appendChild(popup);
    window._gTimePicker = popup;
    window._ctpH = _h; window._ctpM = _m;

    const rect = btn.getBoundingClientRect();
    const pw = 200;
    let top = rect.bottom + 6;
    if (top + 340 > window.innerHeight - 10) top = rect.top - 340 - 6;
    let left = rect.left;
    if (left + pw > window.innerWidth - 10) left = window.innerWidth - pw - 10;
    if (left < 8) left = 8;
    popup.style.top = top + 'px';
    popup.style.left = left + 'px';

    setTimeout(() => {
        const hEl = popup.querySelector(`#ctp-hours [data-v="${_h}"]`);
        const mEl = popup.querySelector(`#ctp-mins [data-v="${_m}"]`);
        if (hEl) hEl.scrollIntoView({block:'center'});
        if (mEl) mEl.scrollIntoView({block:'center'});
    }, 10);

    setTimeout(() => {
        document.addEventListener('click', function _ctpOuter(e) {
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.remove(); window._gTimePicker = null;
                document.removeEventListener('click', _ctpOuter);
            }
        });
    }, 100);
}

function ctpSelect(type, val, el) {
    const colId = type === 'h' ? 'ctp-hours' : 'ctp-mins';
    document.querySelectorAll('#' + colId + ' div').forEach(d => {
        d.style.fontWeight = '400'; d.style.color = '#374151'; d.style.background = 'transparent';
    });
    el.style.fontWeight = '700'; el.style.color = '#f97316'; el.style.background = '#fff7ed';
    if (type === 'h') window._ctpH = val; else window._ctpM = val;
    const t = String(window._ctpH).padStart(2,'0') + ':' + String(window._ctpM).padStart(2,'0');
    document.getElementById('ctp-preview').textContent = t;
}

function ctpConfirm() {
    const t = String(window._ctpH).padStart(2,'0') + ':' + String(window._ctpM).padStart(2,'0');
    document.getElementById('client_training_time_val').value = t;
    const lbl = document.getElementById('client_time_label');
    lbl.textContent = t;
    lbl.style.color = '#f97316';
    if (window._gTimePicker) { window._gTimePicker.remove(); window._gTimePicker = null; }
}
</script>
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
