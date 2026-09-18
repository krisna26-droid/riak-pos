<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Daftar Menu & Inventaris') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Katalog menu, pengaturan modal HPP, harga jual, dan stok bahan.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow transition">
                + Tambah Menu Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]" x-data="{ restockModal: false, selectedProduct: null, productName: '', restockAmount: 10 }">
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

            {{-- Filter Kategori & Pencarian --}}
            <div class="bg-[#FFFDF9] p-4 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col sm:flex-row justify-between items-center gap-3">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..." class="rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs py-1.5 focus:border-[#8C6239] focus:ring-[#8C6239] min-w-[200px]">
                    
                    <select name="category_id" class="rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs py-1.5 focus:border-[#8C6239] focus:ring-[#8C6239]">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3.5 py-1.5 bg-[#8C6239] hover:bg-[#73502E] text-white rounded-xl text-xs font-bold transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'category_id']))
                        <a href="{{ route('admin.products.index') }}" class="text-xs text-[#9E2A2B] hover:underline font-semibold ml-1">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Alert Stok Menipis --}}
            @php
                $lowStockItems = $products->filter(fn($p) => $p->stock <= 10);
            @endphp
            @if ($lowStockItems->count() > 0)
                <div class="bg-[#FFF4E5] border border-[#FFD8A8] p-4 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D9480F] animate-ping"></span>
                        <span class="text-xs font-bold text-[#8C3A00]">
                            Peringatan Inventaris: Terdapat {{ $lowStockItems->count() }} menu dengan persediaan tersisa &le; 10 porsi.
                        </span>
                    </div>
                </div>
            @endif

            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[11px] uppercase text-[#5C4A3E] font-bold tracking-wider">
                                <th class="py-3 px-4">Menu</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-right font-mono">HPP (Modal)</th>
                                <th class="py-3 px-4 text-right font-mono">Harga Jual</th>
                                <th class="py-3 px-4 text-center">Stok</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3ECE4]">
                            @forelse ($products as $product)
                                <tr class="hover:bg-[#FDFBF7] transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-[#EDE4DA] border border-[#D7C7B7] overflow-hidden shrink-0 shadow-xs">
                                                @if ($product->image)
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-[9px] text-[#A6978A] font-bold">NO IMG</div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-bold text-[#2B1810] text-sm block">{{ $product->name }}</span>
                                                <span class="font-mono text-[10px] text-[#8C6239]">{{ $product->sku ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#F5EFEB] text-[#5C4A3E] border border-[#E2D6C8]">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-[#7B6E65]">
                                        Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-[#4A2E1B] text-sm">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="px-2.5 py-0.5 text-xs rounded-full font-bold font-mono {{ $product->stock <= 10 ? 'bg-[#FFF1F0] text-[#9E2A2B] border border-[#F5C2C0]' : 'bg-[#EBFBEE] text-[#2B8A3E]' }}">
                                                {{ $product->stock }}
                                            </span>
                                            <button @click="selectedProduct = {{ $product->id }}; productName = '{{ addslashes($product->name) }}'; restockModal = true" 
                                                    title="Tambah Stok Cepat" 
                                                    class="text-[#8C6239] hover:text-[#4A2E1B] p-1 rounded-lg hover:bg-[#F3ECE4] transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($product->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EBFBEE] text-[#2B8A3E]">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-600">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-[#8C6239] hover:text-[#4A2E1B] font-bold">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini dari katalog?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#9E2A2B] hover:text-rose-900 font-bold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-[#A6978A]">Belum ada data menu yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Quick Restock --}}
        <template x-if="restockModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4">
                <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-sm w-full p-6">
                    <h3 class="font-serif font-bold text-[#2B1810] text-base mb-0.5">Tambah Stok Menu</h3>
                    <p class="text-xs text-[#7B6E65] mb-4">Menu: <span class="font-bold text-[#4A2E1B]" x-text="productName"></span></p>

                    <form :action="'/admin/products/' + selectedProduct + '/quick-restock'" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">Jumlah Tambahan Masuk *</label>
                            <input type="number" name="additional_stock" x-model.number="restockAmount" min="1" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-base font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239]">
                        </div>

                        <div class="flex gap-2.5">
                            <button type="button" @click="restockModal = false" class="flex-1 py-2.5 border border-[#D7C7B7] text-[#5C4A3E] rounded-xl font-semibold text-xs hover:bg-[#F3ECE4] transition">Batal</button>
                            <button type="submit" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white font-bold rounded-xl text-xs shadow transition">Simpan Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>