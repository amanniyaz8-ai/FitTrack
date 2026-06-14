@extends('layouts.app')
@section('title', 'Редактирование пакета')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6 gap-3">
        <a href="{{ route('clients.show', $package->client) }}" class="text-gray-400 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color: #0f2035;">Редактирование пакета</h1>
            <p class="text-gray-500 text-sm">{{ $package->client->full_name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 space-y-5">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-600 text-sm">
            <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('packages.update', $package) }}">
            @csrf
            @method('PATCH')

            {{-- Пакет --}}
            <div class="pb-5 border-b border-gray-100 space-y-4">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-box text-orange-400 mr-2"></i>Параметры пакета</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Количество тренировок</label>
                        <input type="number" name="total_sessions" value="{{ old('total_sessions', $package->total_sessions) }}" min="1" max="100"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Стоимость (₸)</label>
                        <div class="relative">
                            <input type="number" name="price" value="{{ old('price', (float)$package->price) }}" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-sm">₸</span>
                        </div>
                    </div>
                </div>

                {{-- Price per session --}}
                <div class="flex items-center gap-3 bg-orange-50 border border-orange-200 rounded-lg px-4 py-3">
                    <i class="fas fa-calculator text-orange-400"></i>
                    <div>
                        <p class="text-xs text-gray-500">Стоимость одной тренировки</p>
                        <p class="font-bold text-lg whitespace-nowrap" style="color:#0f2035;" id="price-per-session">
                            {{ number_format((float)$package->price / max($package->total_sessions, 1), 0, '.', ' ') }} ₸
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата оплаты</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', $package->payment_date->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_paid" value="1" {{ $package->is_paid ? 'checked' : '' }}
                                class="w-4 h-4 rounded" style="accent-color: #f97316;">
                            <span class="text-sm font-medium text-gray-700">Оплачен</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Тип тренировок --}}
            <div class="pt-5 border-t border-gray-100 space-y-3">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-layer-group text-orange-400 mr-2"></i>Тип тренировок</h2>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="training_type" value="personal"
                            {{ old('training_type', $package->client->training_type ?? 'personal') === 'personal' ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-full text-center py-2.5 rounded-lg border-2 border-gray-200 text-sm font-semibold text-gray-500 transition
                            peer-checked:border-orange-500 peer-checked:text-orange-500 peer-checked:bg-orange-50">
                            <i class="fas fa-user mr-1"></i> Персональные
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="training_type" value="mini_group"
                            {{ old('training_type', $package->client->training_type ?? '') === 'mini_group' ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-full text-center py-2.5 rounded-lg border-2 border-gray-200 text-sm font-semibold text-gray-500 transition
                            peer-checked:border-orange-500 peer-checked:text-orange-500 peer-checked:bg-orange-50">
                            <i class="fas fa-users mr-1"></i> Мини-группа
                        </div>
                    </label>
                </div>
            </div>

            {{-- Время тренировок --}}
            <div class="pt-5 border-t border-gray-100 space-y-3">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-clock text-orange-400 mr-2"></i>Время тренировок</h2>
                <p class="text-xs text-gray-400">При сохранении обновится на всех будущих занятиях клиента</p>
                <input type="hidden" name="training_time" id="pkg_edit_time_val" value="{{ old('training_time', $package->client->training_time ?? '') }}">
                @php $curTime = old('training_time', $package->client->training_time ?? ''); @endphp
                <button type="button" onclick="openPkgTimePicker(this)"
                    class="flex items-center gap-3 w-full bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 hover:bg-orange-100 transition text-left focus:outline-none group">
                    <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0 group-hover:bg-orange-600 transition">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Время занятия</p>
                        <p id="pkg_edit_time_label" class="font-bold text-lg" style="color:#0f2035; line-height:1.2">
                            {{ $curTime ?: '—' }}
                        </p>
                    </div>
                    <i class="fas fa-pen text-orange-400 text-xs"></i>
                </button>
            </div>

            {{-- Дни тренировок клиента --}}
            @php
                $days = ['Mon'=>'Пн','Tue'=>'Вт','Wed'=>'Ср','Thu'=>'Чт','Fri'=>'Пт','Sat'=>'Сб','Sun'=>'Вс'];
                $selectedDays = old('training_days', $package->client->training_days ?? []);
            @endphp
            <div class="pt-5 border-t border-gray-100 space-y-3">
                <h2 class="font-semibold text-gray-700"><i class="fas fa-calendar-week text-orange-400 mr-2"></i>Дни тренировок клиента</h2>
                <p class="text-xs text-gray-400">Изменение дней не пересоздаёт уже созданные занятия — только влияет на новые пакеты</p>
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

            <div class="pt-5 flex gap-3">
                <button type="submit" class="text-white px-6 py-2 rounded-lg font-medium transition"
                    style="background-color:#f97316" onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                    <i class="fas fa-save mr-2"></i>Сохранить
                </button>
                <a href="{{ route('clients.show', $package->client) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<style>
input[type="checkbox"].sr-only:checked + span {
    background-color: #f97316;
    color: white;
    border-color: #f97316;
}
</style>

<script>
function recalc() {
    const price = parseFloat(document.querySelector('[name=price]').value) || 0;
    const sessions = parseInt(document.querySelector('[name=total_sessions]').value) || 1;
    const per = sessions > 0 ? Math.round(price / sessions) : 0;
    document.getElementById('price-per-session').textContent =
        per.toLocaleString('ru-RU') + ' ₸';
}
document.querySelector('[name=price]').addEventListener('input', recalc);
document.querySelector('[name=total_sessions]').addEventListener('input', recalc);

// Time picker for package edit (drum-roll style like dashboard)
function openPkgTimePicker(btn) {
    if (window._pkgTimePicker) { window._pkgTimePicker.remove(); window._pkgTimePicker = null; }
    const current = document.getElementById('pkg_edit_time_val').value;
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
            <span id="pkgtp-preview" style="color:#fb923c;font-size:15px;font-weight:700;">${String(_h).padStart(2,'0')}:${String(_m).padStart(2,'0')}</span>
        </div>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;">
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;border-right:1px solid #f3f4f6;">Часы</div>
            <div style="flex:1;text-align:center;padding:4px 0;font-size:11px;color:#9ca3af;">Минуты</div>
        </div>
        <div style="display:flex;height:210px;">
            <div id="pkgtp-hours" style="${colStyle}border-right:1px solid #f3f4f6;">
                ${hours.map(v=>`<div data-v="${v}" onclick="pkgtpSelect('h',${v},this)" style="${iS(v===_h)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
            <div id="pkgtp-mins" style="${colStyle}">
                ${minutes.map(v=>`<div data-v="${v}" onclick="pkgtpSelect('m',${v},this)" style="${iS(v===_m)}">${String(v).padStart(2,'0')}</div>`).join('')}
            </div>
        </div>
        <div style="padding:10px 12px;">
            <button type="button" onclick="pkgtpConfirm()" style="width:100%;background:#f97316;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;">✓ OK</button>
        </div>`;
    document.body.appendChild(popup);
    window._pkgTimePicker = popup;
    window._pkgtpH = _h; window._pkgtpM = _m;
    const rect = btn.getBoundingClientRect();
    let top = rect.bottom + 6;
    if (top + 340 > window.innerHeight - 10) top = rect.top - 340 - 6;
    let left = rect.left;
    if (left + 200 > window.innerWidth - 10) left = window.innerWidth - 210;
    if (left < 8) left = 8;
    popup.style.top = top + 'px'; popup.style.left = left + 'px';
    setTimeout(() => {
        popup.querySelector(`#pkgtp-hours [data-v="${_h}"]`)?.scrollIntoView({block:'center'});
        popup.querySelector(`#pkgtp-mins [data-v="${_m}"]`)?.scrollIntoView({block:'center'});
    }, 10);
    setTimeout(() => {
        document.addEventListener('click', function _cl(e) {
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.remove(); window._pkgTimePicker = null;
                document.removeEventListener('click', _cl);
            }
        });
    }, 100);
}
function pkgtpSelect(type, val, el) {
    const colId = type === 'h' ? 'pkgtp-hours' : 'pkgtp-mins';
    document.querySelectorAll('#'+colId+' div').forEach(d => { d.style.fontWeight='400'; d.style.color='#374151'; d.style.background='transparent'; });
    el.style.fontWeight='700'; el.style.color='#f97316'; el.style.background='#fff7ed';
    if (type==='h') window._pkgtpH=val; else window._pkgtpM=val;
    document.getElementById('pkgtp-preview').textContent = String(window._pkgtpH).padStart(2,'0')+':'+String(window._pkgtpM).padStart(2,'0');
}
function pkgtpConfirm() {
    const t = String(window._pkgtpH).padStart(2,'0')+':'+String(window._pkgtpM).padStart(2,'0');
    document.getElementById('pkg_edit_time_val').value = t;
    document.getElementById('pkg_edit_time_label').textContent = t;
    if (window._pkgTimePicker) { window._pkgTimePicker.remove(); window._pkgTimePicker = null; }
}
</script>

@endsection
