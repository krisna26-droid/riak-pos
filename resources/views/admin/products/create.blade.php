<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl bg-[#F3ECE4] text-[#5C4A3E] hover:bg-[#EAE0D5] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Tambah Menu Baru') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Daftarkan sajian racikan kopi, minuman, atau makanan ke POS Riak Coffee.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]" x-data="{ 
        previewUrl: null,
        fileChosen(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
    }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm p-6 sm:p-8">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="sku" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Kode SKU (Opsional - Otomatis jika kosong)') }}
                            </label>
                            <input id="sku" name="sku" type="text" placeholder="Contoh: RC-LATTE01" value="{{ old('sku') }}" class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('sku')" />
                        </div>

                        <div>
                            <label for="category_id" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Kategori Menu *') }}
                            </label>
                            <select id="category_id" name="category_id" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('category_id')" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="name" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Nama Menu / Produk *') }}
                            </label>
                            <input id="name" name="name" type="text" placeholder="Contoh: Sea Salt Aren Latte" value="{{ old('name') }}" required autofocus class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label for="cost_price" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Modal Dasar / HPP (Rp) *') }}
                            </label>
                            <input id="cost_price" name="cost_price" type="number" min="0" value="{{ old('cost_price', 0) }}" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('cost_price')" />
                        </div>

                        <div>
                            <label for="selling_price" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Harga Jual Kasir (Rp) *') }}
                            </label>
                            <input id="selling_price" name="selling_price" type="number" min="0" value="{{ old('selling_price', 0) }}" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('selling_price')" />
                        </div>

                        <div>
                            <label for="stock" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                                {{ __('Stok Awal (Porsi) *') }}
                            </label>
                            <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', 50) }}" required class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs">
                            <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('stock')" />
                        </div>

                        <div class="flex items-center mt-6">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="hidden" name="is_active" value="0">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-[#D7C7B7] text-[#4A2E1B] focus:ring-[#8C6239]">
                                <span class="ms-2 text-xs font-bold text-[#2B1810]">Aktifkan Menu untuk Penjualan POS</span>
                            </label>
                        </div>
                    </div>

                    {{-- Upload Berkas Gambar & Alternatif URL --}}
                    <div class="pt-4 border-t border-[#F3ECE4]">
                        <label class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-2">
                            {{ __('Foto Menu (Unggah Berkas atau Masukkan URL)') }}
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 rounded-2xl bg-[#EDE4DA] border-2 border-dashed border-[#D7C7B7] overflow-hidden flex items-center justify-center shrink-0 shadow-inner">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl">
                                    <span class="text-[10px] text-[#A6978A] text-center font-bold px-2">Pratinjau Foto</span>
                                </template>
                            </div>
                            <div class="flex-1 space-y-2.5">
                                <input type="file" name="image" @change="fileChosen" accept="image/png, image/jpeg, image/jpg, image/webp" class="w-full text-xs text-[#5C4A3E] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#4A2E1B] file:text-white hover:file:bg-[#382214] cursor-pointer">
                                <div class="text-[10px] text-[#7B6E65]">Atau gunakan tautan URL gambar luar (opsional):</div>
                                <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..." class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-mono py-2 px-3 focus:border-[#8C6239] focus:ring-[#8C6239]">
                            </div>
                        </div>
                        <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('image')" />
                        <x-input-error class="mt-1 text-xs text-[#9E2A2B]" :messages="$errors->get('image_url')" />
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-[#F3ECE4]">
                        <a href="{{ route('admin.products.index') }}" class="flex-1 py-2.5 text-center border border-[#D7C7B7] text-[#5C4A3E] rounded-xl font-semibold text-xs hover:bg-[#F3ECE4] transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow transition">
                            {{ __('Simpan Menu') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>