<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.users.index') }}" class="p-2 rounded-xl bg-[#F3ECE4] text-[#5C4A3E] hover:bg-[#EAE0D5] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Edit Pengguna: ') . $user->name }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Perbarui informasi kredensial login atau peran staf.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm p-6 sm:p-8">
                <form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Nama Lengkap *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2 px-3 shadow-xs">
                        <x-input-error class="mt-1 text-xs text-[#9E2A2B]" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2 px-3 shadow-xs">
                        <x-input-error class="mt-1 text-xs text-[#9E2A2B]" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <label for="role" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Hak Akses Peran *</label>
                        @php
                            $userCurrentRole = $user->roles->first()?->name;
                        @endphp
                        <select id="role" name="role" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2 px-3 shadow-xs">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $userCurrentRole) === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-1 text-xs text-[#9E2A2B]" :messages="$errors->get('role')" />
                    </div>

                    <div class="pt-3 border-t border-[#F3ECE4]">
                        <span class="text-[11px] text-[#7B6E65] block mb-2 font-medium">Kosongkan kolom kata sandi jika tidak ingin mengganti password lama pengguna.</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="password" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                                <input id="password" name="password" type="password" class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2 px-3 shadow-xs">
                                <x-input-error class="mt-1 text-xs text-[#9E2A2B]" :messages="$errors->get('password')" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Konfirmasi Sandi</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2 px-3 shadow-xs">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-[#F3ECE4]">
                        <a href="{{ route('super-admin.users.index') }}" class="flex-1 py-2.5 text-center border border-[#D7C7B7] text-[#5C4A3E] rounded-xl font-semibold text-xs hover:bg-[#F3ECE4] transition">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow transition">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>