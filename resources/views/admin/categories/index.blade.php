<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Kategori Menu') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Klasifikasi menu kopi, minuman, makanan, dan kudapan Riak Coffee.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow transition">
                + Tambah Kategori
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

            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[11px] uppercase text-[#5C4A3E] font-bold tracking-wider">
                                <th class="py-3 px-4">Nama Kategori</th>
                                <th class="py-3 px-4">Slug URL</th>
                                <th class="py-3 px-4 text-center">Jumlah Menu</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3ECE4]">
                            @forelse ($categories as $category)
                                <tr class="hover:bg-[#FDFBF7] transition">
                                    <td class="py-3 px-4 font-bold text-[#2B1810] text-sm">
                                        {{ $category->name }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[#8C6239]">
                                        {{ $category->slug }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F3ECE4] text-[#4A2E1B]">
                                            {{ $category->products_count ?? $category->products()->count() }} Menu
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#8C6239] hover:text-[#4A2E1B] font-bold">Edit</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini beserta menu di dalamnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#9E2A2B] hover:text-rose-900 font-bold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-[#A6978A]">Belum ada kategori menu yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>