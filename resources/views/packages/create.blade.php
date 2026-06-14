@extends('layouts.app')
@section('title', 'Новый пакет')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="{{ route('clients.show', $client) }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color: #0f2035;">Новый пакет</h1>
            <p class="text-gray-500 text-sm">для {{ $client->full_name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <ul class="text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('packages.store', $client) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Количество тренировок <span class="text-red-500">*</span></label>
                    <input type="number" name="total_sessions" value="{{ old('total_sessions', 10) }}" min="1" max="100"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Стоимость (₸) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="price" value="{{ old('price', 60000) }}" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-8 focus:outline-none focus:ring-2">
                        <span class="absolute right-3 top-2.5 text-gray-400 font-medium">₸</span>
                    </div>
                </div>

                {{-- Статус оплаты --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус оплаты</label>
                    <input type="hidden" name="is_paid" id="is_paid_val" value="{{ old('is_paid', '0') }}">
                    <input type="hidden" name="payment_date" id="payment_date_val" value="{{ old('payment_date', date('Y-m-d')) }}">
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Оплачен --}}
                        <button type="button" id="btn_paid" onclick="setPaidStatus('paid')"
                            class="flex items-center gap-3 rounded-xl border-2 px-4 py-3 transition text-left
                            {{ old('is_paid', '1') == '1' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white' }}">
                            <div id="icon_paid" class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 transition
                                {{ old('is_paid', '1') == '1' ? 'bg-green-500' : 'bg-gray-200' }}">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Статус</p>
                                <p class="font-semibold text-sm" style="color:#0f2035">Оплачен</p>
                            </div>
                        </button>
                        {{-- Оплатит после --}}
                        <button type="button" id="btn_later" onclick="setPaidStatus('later')"
                            class="flex items-center gap-3 rounded-xl border-2 px-4 py-3 transition text-left
                            {{ old('is_paid', '1') != '1' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 bg-white' }}">
                            <div id="icon_later" class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 transition
                                {{ old('is_paid', '1') != '1' ? 'bg-orange-500' : 'bg-gray-200' }}">
                                <i class="fas fa-calendar-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Статус</p>
                                <p class="font-semibold text-sm" style="color:#0f2035">Оплатит после</p>
                            </div>
                        </button>
                    </div>
                    {{-- Дата оплаты (показывается всегда) --}}
                    <div class="mt-3">
                        <label class="block text-xs text-gray-500 mb-1"><i class="fas fa-calendar mr-1 text-orange-400"></i>Дата оплаты</label>
                        <input type="date" id="payment_date_input" value="{{ old('payment_date', date('Y-m-d')) }}"
                            onchange="document.getElementById('payment_date_val').value = this.value"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                    </div>
                </div>
            </div>

            {{-- Тип тренировок --}}
            <div class="pt-4 border-t border-gray-100 space-y-3">
                <label class="block text-sm font-medium text-gray-700"><i class="fas fa-layer-group text-orange-400 mr-1"></i>Тип тренировок</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="training_type" value="personal"
                            {{ old('training_type', $client->training_type ?? 'personal') === 'personal' ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-full text-center py-2.5 rounded-lg border-2 border-gray-200 text-sm font-semibold text-gray-500 transition
                            peer-checked:border-orange-500 peer-checked:text-orange-500 peer-checked:bg-orange-50">
                            <i class="fas fa-user mr-1"></i> Персональные
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="training_type" value="mini_group"
                            {{ old('training_type', $client->training_type ?? '') === 'mini_group' ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-full text-center py-2.5 rounded-lg border-2 border-gray-200 text-sm font-semibold text-gray-500 transition
                            peer-checked:border-orange-500 peer-checked:text-orange-500 peer-checked:bg-orange-50">
                            <i class="fas fa-users mr-1"></i> Мини-группа
                        </div>
                    </label>
                </div>
            </div>

            {{-- Дни тренировок --}}
            @php
                $days = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
                $selectedDays = old('training_days', $client->training_days ?? []);
            @endphp
            <div class="pt-4 border-t border-gray-100 space-y-3">
                <label class="block text-sm font-medium text-gray-700"><i class="fas fa-calendar-week text-orange-400 mr-1"></i>Дни тренировок <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                    @foreach($days as $value => $label)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="training_days[]" value="{{ $value }}"
                            {{ in_array($value, $selectedDays) ? 'checked' : '' }}
                            class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 select-none transition
                            peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 hover:border-orange-400">
                            {{ $label }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Время тренировок --}}
            <div class="pt-4 border-t border-gray-100 space-y-2">
                <label class="block text-sm font-medium text-gray-700"><i class="fas fa-clock text-orange-400 mr-1"></i>Время тренировок</label>
                <input type="hidden" name="training_time" id="pkg_create_time_val" value="{{ old('training_time', $client->training_time ?? '') }}">
                @php $curTime = old('training_time', $client->training_time ?? ''); @endphp
                <button type="button" onclick="openPkgCreateTimePicker(this)"
                    class="flex items-center gap-3 w-full bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 hover:bg-orange-100 transition text-left focus:outline-none group">
                    <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0 group-hover:bg-orange-600 transition">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Время занятия</p>
                        <p id="pkg_create_time_label" class="font-bold text-lg" style="color:#0f2035; line-height:1.2">
                            {{ $curTime ?: '—' }}
                        </p>
                    </div>
                    <i class="fas fa-pen text-orange-400 text-xs"></i>
                </button>
            </div>

            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                После создания занятия будут сгенерированы автоматически на основе выбранных дней
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition" style="background-color: #f97316;" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Создать пакет
                </button>
                <a href="{{ route('clients.show', $client) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Отмена</a>
            </div>
        </form>
    </div>
</div>
<script>
// Payment status toggle
function setPaidStatus(status) {
    const isPaid = status === 'paid';
    document.getElementById('is_paid_val').value = isPaid ? '1' : '0';

    const btnPaid  = document.getElementById('btn_paid');
    const btnLater = document.getElementById('btn_later');
    const iconPaid  = document.getElementById('icon_paid');
    const iconLater = document.getElementById('icon_later');

    if (isPaid) {
        btnPaid.className  = btnPaid.className.replace('border-gray-200 bg-white','').replace('border-green-500 bg-green-50','') + ' border-green-500 bg-green-50';
        btnLater.className = btnLater.className.replace('border-orange-500 bg-orange-50','').replace('border-gray-200 bg-white','') + ' border-gray-200 bg-white';
        iconPaid.className  = iconPaid.className.replace('bg-gray-200','').replace('bg-green-500','') + ' bg-green-500';
        iconLater.className = iconLater.className.replace('bg-orange-500','').replace('bg-gray-200','') + ' bg-gray-200';
    } else {
        btnLater.className = btnLater.className.replace('border-gray-200 bg-white','').replace('border-orange-500 bg-orange-50','') + ' border-orange-500 bg-orange-50';
        btnPaid.className  = btnPaid.className.replace('border-green-500 bg-green-50','').replace('border-gray-200 bg-white','') + ' border-gray-200 bg-white';
        iconLater.className = iconLater.className.replace('bg-gray-200','').replace('bg-orange-500','') + ' bg-orange-500';
        iconPaid.className  = iconPaid.className.replace('bg-green-500','').replace('bg-gray-200','') + ' bg-gray-200';
    }
}
// Init on load
document.addEventListener('DOMContentLoaded', function() {
    const val = document.getElementById('is_paid_val').value;
    setPaidStatus(val === '1' ? 'paid' : 'later');
});

function openPkgCreateTimePicker(btn) {
    if (window._pkgCrTimePicker) { window._pkgCrTimePicker.remove(); window._pkgCrTimePicker = null; }
    const current = document.getElementById('pkg_create_time_val').value;
    let _h = current ? parseInt(current.split(':')[0]) : 9;
    let _m = current ? parseInt(current.split(':')[1]) : 0;
    const hours = Array.from({length:24}, (_,i) => i);
    const minutes = [0,5,10,15,20,25,30,35,40,45,50,55];
    const colStyle = 'flex:1;overflow-y:auto;text-align:center;padding:4px 8px;';
    const iS = (sel) => `padding:8px 0;font-size:17px;font-weight:${sel?'700':'400'};color:${sel?'#f97316':'#374151'};cursor:pointer;border-radius:6px;background:${sel?'#fff7ed':'transparent'};`;
    const popup = document.createElement('div');
    popup.style.cssText = 'position:fixed;z-index:3000;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);overflow:hidden;width:200px;';
    popup.innerHTML = `
        <div style="background:#0f2035;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#fff;font-size:13px;font-weight:600;">Время занятия</span>
            <span id="pkgcr-preview" style="color:#fb923c;font-size:15px;font-weight:700;">${String(_h).padStart(2,'0')}:${String(_m).padStart(2,'0')}</span>
        </div>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;">
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;border-right:1px solid #f3f4f6;">Часы</div>
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;">Минуты</div>
        </div>
        <div style="display:flex;height:210px;">
            <div id="pkgcr-hours" style="${colStyle}border-right:1px solid #f3f4f6;">
                ${hours.map(v=>`<div data-v="${v}" onclick="pkgcrSelect('h',${v},this)" style="${iS(v===_h)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
            <div id="pkgcr-mins" style="${colStyle}">
                ${minutes.map(v=>`<div data-v="${v}" onclick="pkgcrSelect('m',${v},this)" style="${iS(v===_m)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
        </div>
        <div style="padding:10px 12px;">
            <button type="button" onclick="pkgcrConfirm()" style="width:100%;background:#f97316;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;">✓ OK</button>
        </div>`;
    document.body.appendChild(popup);
    window._pkgCrTimePicker = popup;
    window._pkgcrH = _h; window._pkgcrM = _m;
    const rect = btn.getBoundingClientRect();
    let top = rect.bottom + 6;
    if (top + 340 > window.innerHeight - 10) top = rect.top - 340 - 6;
    let left = rect.left;
    if (left + 200 > window.innerWidth - 10) left = window.innerWidth - 210;
    if (left < 8) left = 8;
    popup.style.top = top + 'px'; popup.style.left = left + 'px';
    setTimeout(() => {
        popup.querySelector(`#pkgcr-hours [data-v="${_h}"]`)?.scrollIntoView({block:'center'});
        popup.querySelector(`#pkgcr-mins [data-v="${_m}"]`)?.scrollIntoView({block:'center'});
    }, 10);
    setTimeout(() => {
        document.addEventListener('click', function _cl(e) {
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.remove(); window._pkgCrTimePicker = null;
                document.removeEventListener('click', _cl);
            }
        });
    }, 100);
}
function pkgcrSelect(type, val, el) {
    const colId = type === 'h' ? 'pkgcr-hours' : 'pkgcr-mins';
    document.querySelectorAll('#'+colId+' div').forEach(d => { d.style.fontWeight='400'; d.style.color='#374151'; d.style.background='transparent'; });
    el.style.fontWeight='700'; el.style.color='#f97316'; el.style.background='#fff7ed';
    if (type==='h') window._pkgcrH=val; else window._pkgcrM=val;
    document.getElementById('pkgcr-preview').textContent = String(window._pkgcrH).padStart(2,'0')+':'+String(window._pkgcrM).padStart(2,'0');
}
function pkgcrConfirm() {
    const t = String(window._pkgcrH).padStart(2,'0')+':'+String(window._pkgcrM).padStart(2,'0');
    document.getElementById('pkg_create_time_val').value = t;
    document.getElementById('pkg_create_time_label').textContent = t;
    if (window._pkgCrTimePicker) { window._pkgCrTimePicker.remove(); window._pkgCrTimePicker = null; }
}
</script>
@endsection
