@extends('layouts.app')
@section('title', __('app.profile_title'))

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl font-bold"
             style="background: linear-gradient(135deg, #f97316, #ea580c);">
            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold" style="color:#0f2035;">{{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ __('app.trainer') }} · {{ $user->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- Основная информация --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-5">
            <h2 class="text-base font-semibold mb-5 flex items-center gap-2" style="color:#0f2035;">
                <i class="fas fa-user-circle" style="color:#f97316;"></i> {{ __('app.basic_info') }}
            </h2>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                           {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }}">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                           {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Смена пароля --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-5">
            <h2 class="text-base font-semibold mb-1 flex items-center gap-2" style="color:#0f2035;">
                <i class="fas fa-lock" style="color:#f97316;"></i> {{ __('app.change_password') }}
            </h2>
            <p class="text-xs text-gray-400 mb-5">{{ __('app.leave_empty_password') }}</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.current_password') }}</label>
                <div class="relative">
                    <input type="password" name="current_password" id="cur-pwd"
                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 pr-10
                               {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-200' }}"
                        placeholder="{{ __('app.enter_current_pwd') }}">
                    <button type="button" onclick="togglePwd('cur-pwd', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.new_password') }}</label>
                <div class="relative">
                    <input type="password" name="password" id="new-pwd"
                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 pr-10
                               {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }}"
                        placeholder="{{ __('app.min_8_chars') }}">
                    <button type="button" onclick="togglePwd('new-pwd', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.confirm_password') }}</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="conf-pwd"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 pr-10"
                        placeholder="{{ __('app.repeat_new_pwd') }}">
                    <button type="button" onclick="togglePwd('conf-pwd', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Кнопка сохранить --}}
        <button type="submit"
            class="w-full text-white font-semibold py-3 rounded-xl transition text-sm"
            style="background-color:#f97316;"
            onmouseover="this.style.backgroundColor='#ea580c'"
            onmouseout="this.style.backgroundColor='#f97316'">
            <i class="fas fa-save mr-2"></i>{{ __('app.save_changes') }}
        </button>
    </form>

</div>

<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}
</script>
@endsection
