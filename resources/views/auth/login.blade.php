<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                {{ __('Alamat Email') }}
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username" 
                   placeholder="kasir@riakcoffee.test"
                   class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-sm text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-[#9E2A2B]" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                {{ __('Kata Sandi') }}
            </label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="current-password" 
                   placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                   class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-sm text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-[#9E2A2B]" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="rounded border-[#D7C7B7] text-[#4A2E1B] focus:ring-[#8C6239] w-4 h-4 transition">
                <span class="ms-2 text-xs text-[#7B6E65]">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-[#8C6239] hover:text-[#4A2E1B] font-medium transition" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3 bg-[#4A2E1B] hover:bg-[#382214] text-[#FFFDF9] font-bold rounded-xl text-xs uppercase tracking-wider shadow-md transition duration-150 active:scale-[0.99]">
                {{ __('Masuk ke Kasir / Sistem') }}
            </button>
        </div>
    </form>
</x-guest-layout>