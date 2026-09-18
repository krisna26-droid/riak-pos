<nav x-data="{ open: false }" class="bg-[#2B1810] border-b border-[#3D2318] text-[#F5EFEB] shadow-md sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo & Brand Name -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/riaklogo.jpeg') }}" 
                            alt="Riak Coffee Logo" 
                            class="h-10 w-auto object-contain rounded-lg bg-[#FFFDF9] p-0.5 border border-[#4A2E1B] shadow-xs group-hover:opacity-90 transition">
                        <div class="flex flex-col">
                            <span class="font-serif font-bold text-base tracking-widest text-[#FFFDF9] leading-tight group-hover:text-[#D7C7B7] transition">
                                RIAK COFFEE
                            </span>
                            <span class="text-[9px] uppercase tracking-wider text-[#A6978A] -mt-0.5">
                                Coffee & Lake
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-1.5 sm:-my-px sm:ms-8 sm:flex items-center">
                    {{-- Layar Kasir / POS --}}
                    @hasanyrole('cashier|admin|super-admin')
                        <a href="{{ route('pos.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('pos.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Layar Kasir (POS)') }}
                        </a>
                    @endhasanyrole

                    {{-- Modul Admin (Kategori, Menu, Laporan, Shift) --}}
                    @hasanyrole('admin|super-admin')
                        <a href="{{ route('admin.categories.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Kategori') }}
                        </a>
                        <a href="{{ route('admin.products.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Kelola Menu') }}
                        </a>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Laporan Finansial') }}
                        </a>
                        <a href="{{ route('admin.shifts.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('admin.shifts.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Audit Shift') }}
                        </a>
                    @endhasanyrole

                    {{-- Khusus Super Admin --}}
                    @role('super-admin')
                        <a href="{{ route('super-admin.users.index') }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 {{ request()->routeIs('super-admin.users.*') ? 'bg-[#8C6239] text-white shadow-sm' : 'text-[#D7C7B7] hover:bg-[#3D2318] hover:text-[#FFFDF9]' }}">
                            {{ __('Pengguna') }}
                        </a>
                    @endrole
                </div>
            </div>

            <!-- User Menu / Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-xl bg-[#3D2318] hover:bg-[#4A2E1B] border border-[#503022] text-xs font-medium text-[#F5EFEB] focus:outline-none transition duration-150 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <div class="text-left">
                                <span class="font-bold text-[#FFFDF9] block leading-tight">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-[#C2B4A7] block font-mono uppercase tracking-wider">
                                    @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
                                    {{ $user->getRoleNames()->first() ?? 'Staff' }}
                                </span>
                            </div>
                            <svg class="fill-current h-3.5 w-3.5 text-[#A6978A] ms-1" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-[#FFFDF9] rounded-xl shadow-xl border border-[#E8DFD8] py-1 text-xs divide-y divide-[#F3ECE4]">
                            <div class="px-4 py-2">
                                <span class="font-bold text-[#2B1810] block truncate">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-[#7B6E65] font-mono block truncate">{{ Auth::user()->email }}</span>
                            </div>

                            <div class="py-1">
                                <x-dropdown-link :href="route('profile.edit')" class="text-[#5C4A3E] hover:bg-[#F5EFEB] font-semibold">
                                    {{ __('Edit Profil') }}
                                </x-dropdown-link>
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-[#9E2A2B] hover:bg-[#FFF1F0] font-bold">
                                        {{ __('Keluar (Log Out)') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-[#D7C7B7] hover:text-white hover:bg-[#3D2318] focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#24140D] border-b border-[#3D2318] px-4 pt-2 pb-4 space-y-1.5">
        @hasanyrole('cashier|admin|super-admin')
            <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pos.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Layar Kasir (POS)') }}
            </a>
        @endhasanyrole

        @hasanyrole('admin|super-admin')
            <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('admin.categories.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Kategori Menu') }}
            </a>
            <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('admin.products.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Kelola Menu') }}
            </a>
            <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('admin.reports.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Laporan Finansial') }}
            </a>
            <a href="{{ route('admin.shifts.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('admin.shifts.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Audit Shift Kasir') }}
            </a>
        @endhasanyrole

        @role('super-admin')
            <a href="{{ route('super-admin.users.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('super-admin.users.*') ? 'bg-[#8C6239] text-white' : 'text-[#D7C7B7] hover:bg-[#3D2318]' }}">
                {{ __('Manajemen Pengguna') }}
            </a>
        @endrole

        <div class="pt-3 border-t border-[#3D2318] mt-2">
            <div class="text-xs font-bold text-[#FFFDF9]">{{ Auth::user()->name }}</div>
            <div class="text-[10px] text-[#A6978A] font-mono">{{ Auth::user()->email }}</div>

            <div class="mt-3 flex gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 py-2 text-center bg-[#3D2318] hover:bg-[#4A2E1B] text-[#D7C7B7] rounded-xl text-xs font-semibold">
                    {{ __('Profil') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2 text-center bg-[#9E2A2B] hover:bg-[#852324] text-white rounded-xl text-xs font-bold shadow-xs">
                        {{ __('Keluar') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>