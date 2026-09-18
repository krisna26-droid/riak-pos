<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Manajemen Akses & Staf') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Otorisasi peran pengguna (Super Admin, Admin, dan Kasir POS).</p>
            </div>
            <a href="{{ route('super-admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow transition">
                + Tambah Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold shadow-xs">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[11px] uppercase text-[#5C4A3E] font-bold tracking-wider">
                                <th class="py-3 px-4">Nama Staf</th>
                                <th class="py-3 px-4">Email Login</th>
                                <th class="py-3 px-4 text-center">Peran Akses</th>
                                <th class="py-3 px-4">Terdaftar Sejak</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3ECE4]">
                            @forelse ($users as $user)
                                <tr class="hover:bg-[#FDFBF7] transition">
                                    <td class="py-3 px-4 font-bold text-[#2B1810]">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="ms-1 px-1.5 py-0.5 rounded text-[9px] bg-[#F3ECE4] text-[#8C6239]">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[#5C4A3E]">
                                        {{ $user->email }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $roleName = $user->roles->first()?->name;
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $roleName === 'super-admin' ? 'bg-[#4A2E1B] text-white' : ($roleName === 'admin' ? 'bg-[#8C6239] text-white' : 'bg-[#EBFBEE] text-[#2B8A3E]') }}">
                                            {{ $roleName ?? 'Tanpa Peran' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-[#7B6E65] font-mono">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <a href="{{ route('super-admin.users.edit', $user) }}" class="text-[#8C6239] hover:text-[#4A2E1B] font-bold">Edit</a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#9E2A2B] hover:text-rose-900 font-bold">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-[#A6978A]">Tidak ada data pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>