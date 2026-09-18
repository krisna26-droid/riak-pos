<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" class="p-2 rounded-xl bg-[#F3ECE4] text-[#5C4A3E] hover:bg-[#EAE0D5] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Edit Kategori: ') . $category->name }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Perbarui nama kategori kelompok menu.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm p-6 sm:p-8">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">
                            {{ __('Nama Kategori *') }}
                        </label>
                        <input id="name" 
                               name="name" 
                               type="text" 
                               value="{{ old('name', $category->name) }}" 
                               required 
                               autofocus 
                               class="w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239] py-2.5 px-3.5 shadow-xs transition" />
                        <x-input-error class="mt-1.5 text-xs text-[#9E2A2B]" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-[#F3ECE4]">
                        <a href="{{ route('admin.categories.index') }}" class="flex-1 py-2.5 text-center border border-[#D7C7B7] text-[#5C4A3E] rounded-xl font-semibold text-xs hover:bg-[#F3ECE4] transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow transition">
                            {{ __('Perbarui Kategori') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>