<x-app-layout>
    <div x-data="posApp()" x-init="init()" class="relative h-[calc(100vh-65px)] flex flex-col bg-[#F9F6F0] font-sans antialiased overflow-hidden select-none">
        
        {{-- ================= MODAL BUKA SHIFT ================= --}}
        <div x-show="!activeShift" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-md w-full p-5 sm:p-6 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-[#F3ECE4] text-[#8C6239] rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 border border-[#E0D0C1]">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-serif font-bold text-[#2B1810]">Buka Shift Kasir</h3>
                <p class="text-xs text-[#7B6E65] mt-1 mb-5">Masukkan modal kas awal di laci kasir sebelum memulai pesanan.</p>
                
                <div class="text-left mb-5">
                    <label class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">Modal Kas Awal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#8C6239] font-bold text-sm">Rp</span>
                        <input type="number" x-model.number="startingCash" placeholder="0" class="pl-11 w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] focus:border-[#8C6239] focus:ring-[#8C6239] text-base sm:text-lg font-bold text-[#2B1810]">
                    </div>
                </div>

                <button @click="openShift()" :disabled="startingCash === null || startingCash < 0" class="w-full py-3 bg-[#4A2E1B] hover:bg-[#382214] disabled:opacity-50 text-[#F5EFEB] font-semibold rounded-xl shadow-md transition duration-150 text-sm">
                    Buka Kasir Sekarang
                </button>
            </div>
        </div>

        {{-- ================= MODAL TUTUP SHIFT ================= --}}
        <div x-show="showCloseShiftModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-md w-full p-5 sm:p-6">
                <h3 class="text-lg font-serif font-bold text-[#2B1810] mb-1">Rekonsiliasi & Tutup Shift</h3>
                <p class="text-xs text-[#7B6E65] mb-4">Hitung seluruh uang fisik tunai yang ada di dalam laci kasir.</p>

                <div class="bg-[#F5EFEB] border border-[#E2D6C8] rounded-xl p-3 text-xs space-y-1.5 mb-4">
                    <div class="flex justify-between text-[#5C4A3E]">
                        <span>Modal Kas Awal:</span>
                        <span class="font-semibold text-[#2B1810]" x-text="formatRupiah(activeShift?.starting_cash || 0)"></span>
                    </div>
                    <div class="flex justify-between text-[#7B6E65]">
                        <span>Waktu Buka Shift:</span>
                        <span x-text="activeShift ? new Date(activeShift.opened_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-'"></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1.5">Fisik Kas Terhitung</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#8C6239] font-bold text-sm">Rp</span>
                        <input type="number" x-model.number="actualEndingCash" placeholder="0" class="pl-11 w-full rounded-xl border-[#D7C7B7] bg-[#FDFBF7] focus:border-[#8C6239] focus:ring-[#8C6239] text-base sm:text-lg font-bold text-[#2B1810]">
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button @click="showCloseShiftModal = false" class="flex-1 py-2.5 border border-[#D7C7B7] text-[#5C4A3E] rounded-xl font-semibold text-xs hover:bg-[#F3ECE4] transition">Batal</button>
                    <button @click="closeShift()" :disabled="actualEndingCash === null || actualEndingCash < 0" class="flex-1 py-2.5 bg-[#9E2A2B] hover:bg-[#852324] disabled:opacity-50 text-white font-semibold rounded-xl text-xs shadow transition">Tutup Shift</button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PILIH VARIAN SUHU (HOT / ICE) ================= --}}
        <div x-show="temperatureModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div @click.away="temperatureModal.open = false" class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-sm w-full p-5 sm:p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-[#F3ECE4] text-[#8C6239] flex items-center justify-center mx-auto mb-3 font-serif font-bold text-xl">
                    ☕
                </div>
                <h3 class="font-serif font-bold text-[#2B1810] text-base" x-text="temperatureModal.product?.name"></h3>
                <p class="text-xs text-[#7B6E65] mt-0.5 mb-4">Pilih varian untuk menu ini:</p>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div x-show="temperatureModal.hotVariant">
                        <button type="button" 
                                @click="confirmAddToCart('hot')"
                                class="w-full py-3 px-3 rounded-xl bg-orange-50 hover:bg-orange-100 border-2 border-orange-200 text-orange-950 font-bold text-xs flex flex-col items-center gap-1 transition active:scale-95 shadow-sm">
                            <span class="text-2xl">🔥</span>
                            <span>HOT</span>
                            <span class="text-[11px] font-mono font-extrabold text-orange-800" x-text="formatRupiah(temperatureModal.hotVariant?.price || 0)"></span>
                        </button>
                    </div>

                    <div x-show="temperatureModal.iceVariant">
                        <button type="button" 
                                @click="confirmAddToCart('ice')"
                                class="w-full py-3 px-3 rounded-xl bg-cyan-50 hover:bg-cyan-100 border-2 border-cyan-200 text-cyan-950 font-bold text-xs flex flex-col items-center gap-1 transition active:scale-95 shadow-sm">
                            <span class="text-2xl">🧊</span>
                            <span>ICE</span>
                            <span class="text-[11px] font-mono font-extrabold text-cyan-800" x-text="formatRupiah(temperatureModal.iceVariant?.price || 0)"></span>
                        </button>
                    </div>
                </div>

                <button type="button" @click="temperatureModal.open = false" class="w-full py-2 text-xs text-[#7B6E65] hover:text-[#2B1810] font-semibold">
                    Batal
                </button>
            </div>
        </div>

        {{-- ================= MODAL STRUK CETAK ================= --}}
        <div x-show="completedOrder" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/80 backdrop-blur-sm p-4 overflow-y-auto" style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5 sm:p-6 text-center my-auto">
                <div id="thermal-receipt" class="bg-white text-black text-left font-mono p-4 border border-stone-200 rounded-lg text-xs">
                    <div class="text-center pb-2">
                        <img src="{{ asset('images/riaklogo.jpeg') }}" 
                             alt="Riak Coffee" 
                             class="mx-auto h-16 w-auto object-contain mb-1 filter grayscale contrast-125">
                        <h2 class="font-extrabold text-sm tracking-wider text-black font-serif">RIAK COFFEE</h2>
                        <p class="text-[9px] text-stone-600">Jalan ke Dasong, Pancasari, Kec. Sukasada, Buleleng, Bali</p>
                        <p class="text-[9px] text-stone-600">WA: +62 821-4480-8213</p>
                    </div>

                    <div class="border-t border-dashed border-stone-400 my-2"></div>

                    <div class="flex items-center justify-between py-1 bg-stone-100 px-2 rounded">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-stone-500 block">MEJA / TABLE</span>
                            <span class="text-2xl font-black text-black font-mono leading-none" x-text="completedOrder?.table_number || 'TAKEAWAY'"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] uppercase tracking-wider text-stone-500 block">STATUS</span>
                            <span class="text-xs font-bold font-mono uppercase px-2 py-0.5 rounded" 
                                  :class="completedOrder?.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                  x-text="completedOrder?.status === 'paid' ? 'LUNAS' : 'OPEN BILL'">
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-stone-400 my-2"></div>

                    <div class="text-[11px] space-y-0.5">
                        <div class="flex justify-between">
                            <span>No. Inv:</span>
                            <span class="font-bold" x-text="completedOrder?.invoice_number"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu:</span>
                            <span x-text="new Date().toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}) + ' ' + new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kasir:</span>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Metode:</span>
                            <span class="font-bold uppercase" x-text="completedOrder?.payment_method === 'later' ? 'BAYAR NANTI' : completedOrder?.payment_method"></span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-stone-400 my-2"></div>

                    <div class="space-y-1.5 text-[11px]">
                        <template x-for="item in (completedOrder?.items || [])" :key="item.id">
                            <div>
                                <div class="font-semibold text-stone-900" x-text="(item.product ? item.product.name : (item.name || 'Menu')) + (item.temperature ? ' (' + item.temperature.toUpperCase() + ')' : '')"></div>
                                <div class="flex justify-between text-stone-600 pl-2">
                                    <span x-text="item.quantity + ' x ' + formatRupiah(item.price)"></span>
                                    <span class="font-mono text-stone-900" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-dashed border-stone-400 my-2"></div>

                    <div class="space-y-1 text-[11px]">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-mono" x-text="formatRupiah(completedOrder?.subtotal || 0)"></span>
                        </div>
                        <div class="flex justify-between text-rose-600" x-show="completedOrder?.discount > 0">
                            <span>Diskon</span>
                            <span class="font-mono" x-text="'-' + formatRupiah(completedOrder?.discount || 0)"></span>
                        </div>
                        <div class="flex justify-between" x-show="completedOrder?.tax > 0">
                            <span>PPN (11%)</span>
                            <span class="font-mono" x-text="formatRupiah(completedOrder?.tax || 0)"></span>
                        </div>
                        <div class="flex justify-between font-extrabold text-xs pt-1 border-t border-dotted border-stone-400">
                            <span>TOTAL TAGIHAN</span>
                            <span class="font-mono text-sm" x-text="formatRupiah(completedOrder?.grand_total || 0)"></span>
                        </div>
                        <div class="flex justify-between pt-1" x-show="completedOrder?.payment_method === 'cash'">
                            <span>Tunai Diterima</span>
                            <span class="font-mono" x-text="formatRupiah(completedOrder?.cash_received || 0)"></span>
                        </div>
                        <div class="flex justify-between font-bold" x-show="completedOrder?.payment_method === 'cash'">
                            <span>Kembalian</span>
                            <span class="font-mono" x-text="formatRupiah(completedOrder?.cash_change || 0)"></span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-stone-400 my-3"></div>

                    <div class="text-center text-[10px] text-stone-600 space-y-0.5 pt-1">
                        <p class="font-semibold">Terima Kasih Atas Kunjungan Anda!</p>
                        <p>Nikmati suasana dan aroma kopi kami :)</p>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button onclick="window.print()" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold shadow transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak Nota
                    </button>
                    <button @click="completedOrder = null" class="flex-1 py-2.5 bg-[#F3ECE4] hover:bg-[#EAE0D5] text-[#5C4A3E] rounded-xl text-xs font-semibold">
                        Selesai
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL RIWAYAT TRANSAKSI ================= --}}
        <div x-show="showRecentOrdersModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-2xl w-full p-5 sm:p-6 flex flex-col max-h-[85vh]">
                <div class="flex justify-between items-center pb-3 border-b border-[#E8DFD8]">
                    <div>
                        <h3 class="font-serif font-bold text-[#2B1810] text-base">Riwayat Nota / Transaksi Terakhir</h3>
                        <p class="text-[11px] text-[#7B6E65]">Daftar transaksi kasir untuk dicetak ulang struknya.</p>
                    </div>
                    <button @click="showRecentOrdersModal = false" class="text-[#7B6E65] hover:text-[#2B1810] font-bold text-xl">&times;</button>
                </div>

                <div class="flex-1 overflow-y-auto py-3 space-y-2 no-scrollbar">
                    <div x-show="loadingRecent" class="py-10 text-center text-xs text-[#7B6E65]">Memuat nota...</div>
                    <template x-for="order in recentOrders" :key="order.id">
                        <div class="p-3 bg-white rounded-xl border border-[#E8DFD8] flex items-center justify-between gap-3 hover:border-[#8C6239] transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-[#2B1810]" x-text="order.invoice_number"></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                          :class="order.status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                                          x-text="order.status"></span>
                                    <span x-show="order.table_number" class="px-1.5 py-0.5 rounded bg-[#F3ECE4] text-[#4A2E1B] text-[10px] font-bold" x-text="'Meja ' + order.table_number"></span>
                                </div>
                                <div class="text-[11px] text-[#7B6E65] mt-1">
                                    <span x-text="new Date(order.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span> &bull; 
                                    <span class="uppercase font-semibold" x-text="order.payment_method"></span> &bull; 
                                    <span class="font-mono font-bold text-[#2B1810]" x-text="formatRupiah(order.grand_total)"></span>
                                </div>
                            </div>
                            <button @click="completedOrder = order; showRecentOrdersModal = false" class="px-3 py-1.5 bg-[#4A2E1B] hover:bg-[#382214] text-white text-xs font-bold rounded-lg transition shrink-0">
                                Cetak Nota
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ================= MODAL TAGIHAN BELUM LUNAS ================= --}}
        <div x-show="showPendingOrdersModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-2xl w-full p-5 sm:p-6 flex flex-col max-h-[85vh]">
                <div class="flex justify-between items-center pb-3 border-b border-[#E8DFD8]">
                    <div>
                        <h3 class="font-serif font-bold text-[#2B1810] text-base">Tagihan Belum Lunas (Bayar Nanti / Open Bill)</h3>
                        <p class="text-[11px] text-[#7B6E65]">Pilih meja untuk menambah pesanan ke nota yang sama atau selesaikan pembayaran.</p>
                    </div>
                    <button @click="showPendingOrdersModal = false" class="text-[#7B6E65] hover:text-[#2B1810] font-bold text-xl">&times;</button>
                </div>

                <div class="flex-1 overflow-y-auto py-3 space-y-2.5 no-scrollbar">
                    <div x-show="loadingPending" class="py-10 text-center text-xs text-[#7B6E65]">Memuat data meja pending...</div>
                    <div x-show="!loadingPending && pendingOrders.length === 0" class="py-10 text-center text-xs text-[#A6978A]">Tidak ada tagihan pending saat ini.</div>
                    
                    <template x-for="order in pendingOrders" :key="order.id">
                        <div class="p-3.5 bg-white rounded-xl border border-[#E8DFD8] hover:border-[#8C6239] transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-[#8C6239] font-mono px-2.5 py-0.5 bg-[#F3ECE4] rounded-lg" x-text="order.table_number ? 'Meja: ' + order.table_number : 'Takeaway'"></span>
                                    <span class="font-mono text-xs text-[#7B6E65]" x-text="order.invoice_number"></span>
                                </div>
                                <div class="text-xs text-[#5C4A3E] mt-1.5">
                                    Total Saat Ini: <span class="font-mono font-black text-[#2B1810] text-sm" x-text="formatRupiah(order.grand_total)"></span>
                                    <span class="text-[10px] text-[#A6978A]" x-text="'(' + order.items.length + ' jenis menu)'"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button @click="targetAppendOrder = order; showPendingOrdersModal = false; mobileCartOpen = true;" 
                                        class="px-3 py-1.5 border border-[#8C6239] text-[#8C6239] hover:bg-[#F3ECE4] text-xs font-bold rounded-xl transition">
                                    + Tambah Menu
                                </button>
                                <button @click="settleTargetOrder = order; settlePaymentMethod = 'cash'; settleCashReceived = order.grand_total; showSettleModal = true; showPendingOrdersModal = false;" 
                                        class="px-3.5 py-1.5 bg-[#2B8A3E] hover:bg-[#237032] text-white text-xs font-bold rounded-xl shadow transition">
                                    Pelunasan
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PELUNASAN ================= --}}
        <div x-show="showSettleModal && settleTargetOrder" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-sm w-full p-5 sm:p-6 text-left">
                <h3 class="font-serif font-bold text-base text-[#2B1810]">Pelunasan Tagihan Meja</h3>
                <p class="text-xs text-[#7B6E65] mt-0.5">Meja: <span class="font-bold text-[#2B1810]" x-text="settleTargetOrder?.table_number || '-'"></span> &bull; <span class="font-mono" x-text="settleTargetOrder?.invoice_number"></span></p>

                <div class="my-4 p-3 bg-[#FDFBF7] rounded-xl border border-[#E8DFD8]">
                    <span class="text-[10px] font-bold text-[#8C6239] uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-xl font-mono font-black text-[#2B1810]" x-text="formatRupiah(settleTargetOrder?.grand_total || 0)"></span>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-[#5C4A3E] mb-1">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" @click="settlePaymentMethod = 'cash'" :class="settlePaymentMethod === 'cash' ? 'bg-[#4A2E1B] text-white' : 'bg-white border text-[#5C4A3E]'" class="py-1.5 rounded-lg font-bold">Tunai</button>
                            <button type="button" @click="settlePaymentMethod = 'qris'" :class="settlePaymentMethod === 'qris' ? 'bg-[#4A2E1B] text-white' : 'bg-white border text-[#5C4A3E]'" class="py-1.5 rounded-lg font-bold">QRIS</button>
                            <button type="button" @click="settlePaymentMethod = 'transfer'" :class="settlePaymentMethod === 'transfer' ? 'bg-[#4A2E1B] text-white' : 'bg-white border text-[#5C4A3E]'" class="py-1.5 rounded-lg font-bold">Transfer</button>
                        </div>
                    </div>

                    <div x-show="settlePaymentMethod === 'cash'">
                        <label class="block font-bold text-[#5C4A3E] mb-1">Uang Diterima</label>
                        <input type="number" x-model.number="settleCashReceived" class="w-full text-xs font-mono font-bold rounded-lg border-[#D7C7B7]">
                        <div class="flex justify-between mt-1 font-bold">
                            <span>Kembalian:</span>
                            <span :class="(settleCashReceived - (settleTargetOrder?.grand_total || 0)) >= 0 ? 'text-[#2D6A4F]' : 'text-[#9E2A2B]'" class="font-mono" x-text="formatRupiah(settleCashReceived - (settleTargetOrder?.grand_total || 0))"></span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mt-5">
                    <button type="button" @click="showSettleModal = false" class="flex-1 py-2 text-xs border rounded-xl font-semibold text-[#5C4A3E]">Batal</button>
                    <button type="button" @click="executeSettleOrder()" :disabled="settlePaymentMethod === 'cash' && (settleCashReceived < (settleTargetOrder?.grand_total || 0))" class="flex-1 py-2 text-xs bg-[#2B8A3E] hover:bg-[#237032] text-white rounded-xl font-bold shadow disabled:opacity-50">Lunasi &amp; Cetak</button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL BANTUAN TOMBOL PINTAS ================= --}}
        <div x-show="showHelpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-md w-full p-6 text-left">
                <div class="flex justify-between items-center pb-3 border-b border-[#E8DFD8]">
                    <h3 class="font-serif font-bold text-[#2B1810] text-base">Pintasan Tombol Kasir (Hotkeys)</h3>
                    <button @click="showHelpModal = false" class="text-[#7B6E65] hover:text-[#2B1810] font-bold text-lg">&times;</button>
                </div>

                <div class="py-4 space-y-3 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-[#F3ECE4]">
                        <span class="text-[#5C4A3E]">Fokus ke Kolom Pencarian Menu</span>
                        <kbd class="px-2 py-1 bg-[#F5EFEB] border border-[#D7C7B7] rounded text-[11px] font-mono font-bold text-[#2B1810]">F2 atau /</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-[#F3ECE4]">
                        <span class="text-[#5C4A3E]">Pilih Pembayaran Tunai &amp; Buka Kasir</span>
                        <kbd class="px-2 py-1 bg-[#F5EFEB] border border-[#D7C7B7] rounded text-[11px] font-mono font-bold text-[#2B1810]">F4</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-[#F3ECE4]">
                        <span class="text-[#5C4A3E]">Buka Tagihan Belum Lunas (Bayar Nanti)</span>
                        <kbd class="px-2 py-1 bg-[#F5EFEB] border border-[#D7C7B7] rounded text-[11px] font-mono font-bold text-[#2B1810]">F8</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-[#F3ECE4]">
                        <span class="text-[#5C4A3E]">Tutup Laci Keranjang / Modal</span>
                        <kbd class="px-2 py-1 bg-[#F5EFEB] border border-[#D7C7B7] rounded text-[11px] font-mono font-bold text-[#2B1810]">Escape</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-[#5C4A3E]">Tampilkan Panduan Tombol Ini</span>
                        <kbd class="px-2 py-1 bg-[#F5EFEB] border border-[#D7C7B7] rounded text-[11px] font-mono font-bold text-[#2B1810]">F1 atau ?</kbd>
                    </div>
                </div>

                <button @click="showHelpModal = false" class="w-full py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold transition">
                    Mengerti
                </button>
            </div>
        </div>

        {{-- ================= TAMPILAN UTAMA ================= --}}
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden relative">
            
            {{-- SEKSI KATALOG MENU --}}
            <div class="flex-1 flex flex-col overflow-hidden pb-16 lg:pb-0">
                
                {{-- Header Pencarian & Info Shift --}}
                <div class="p-2.5 sm:p-3 bg-[#FFFDF9] border-b border-[#E8DFD8] flex flex-wrap items-center justify-between gap-2.5 shrink-0">
                    <div class="flex items-center gap-2 flex-1 min-w-[280px]">
                        <div class="relative flex-1 max-w-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[#8C6239]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input x-ref="searchInput" 
                                   type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Cari menu (F2 atau /)..." 
                                   class="pl-9 pr-7 py-1.5 w-full rounded-full border-[#D7C7B7] bg-[#FDFBF7] text-xs font-semibold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239]">
                            <button x-show="searchQuery.length > 0" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-xs text-[#A6978A] hover:text-[#2B1810]">
                                &times;
                            </button>
                        </div>

                        <button type="button" 
                                @click="openRecentOrdersModal()" 
                                title="Riwayat Nota / Transaksi Terakhir"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-[#D7C7B7] bg-[#FDFBF7] text-[#5C4A3E] hover:bg-[#F3ECE4] transition text-xs font-bold shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="hidden sm:inline">Riwayat Nota</span>
                        </button>

                        <button type="button" 
                                @click="openPendingOrdersModal()" 
                                title="Tagihan Belum Lunas (Bayar Nanti)"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-[#D7C7B7] bg-[#FFF8E7] text-[#8C6239] hover:bg-[#FDF0D5] transition text-xs font-bold shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Bayar Nanti</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-[#8C6239] text-white text-[10px]" x-text="pendingCount"></span>
                        </button>

                        <button type="button" 
                                @click="showHelpModal = true" 
                                title="Panduan Tombol Pintas"
                                class="hidden sm:flex items-center justify-center w-7 h-7 rounded-full border border-[#D7C7B7] bg-[#FDFBF7] text-[#8C6239] hover:bg-[#F3ECE4] transition text-xs font-mono font-bold shrink-0">
                            ?
                        </button>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <div class="text-right hidden md:block" x-show="activeShift">
                            <span class="text-[9px] uppercase font-bold text-[#8C6239] tracking-wider block">Shift Kasir Aktif</span>
                            <span class="text-xs font-bold text-[#2B1810] font-mono" x-text="'Modal: ' + formatRupiah(activeShift?.starting_cash || 0)"></span>
                        </div>
                        <button @click="showCloseShiftModal = true" class="px-3 py-1.5 bg-[#FFF1F0] border border-[#F5C2C0] text-[#9E2A2B] hover:bg-[#FDE2E1] rounded-lg text-[11px] font-bold transition whitespace-nowrap">
                            Tutup Shift
                        </button>
                    </div>
                </div>

                {{-- Bilah Kategori --}}
                <div class="px-3 sm:px-5 py-2 bg-[#FFFDF9] border-b border-[#E8DFD8] flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <button @click="selectedCategory = null" 
                            :class="selectedCategory === null ? 'bg-[#4A2E1B] text-[#FDFBF7] shadow-sm font-bold' : 'bg-[#F3ECE4] text-[#5C4A3E] hover:bg-[#EAE0D5]'" 
                            class="px-3.5 py-1.5 rounded-full text-xs transition whitespace-nowrap shrink-0">
                        Semua Menu
                    </button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button @click="selectedCategory = cat.id" 
                                :class="selectedCategory === cat.id ? 'bg-[#4A2E1B] text-[#FDFBF7] shadow-sm font-bold' : 'bg-[#F3ECE4] text-[#5C4A3E] hover:bg-[#EAE0D5]'" 
                                class="px-3.5 py-1.5 rounded-full text-xs transition whitespace-nowrap shrink-0 flex items-center gap-1.5">
                            <span x-text="cat.name"></span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-[#E8DFD8] text-[#4A2E1B]" x-text="getCategoryCount(cat.id)"></span>
                        </button>
                    </template>
                </div>

                {{-- Banner Tambah Menu --}}
                <div x-show="targetAppendOrder" class="bg-amber-100 border-b border-amber-300 px-4 py-2 flex items-center justify-between text-xs text-amber-900 font-bold shrink-0" style="display: none;">
                    <div>
                        <span>Mode Penambahan Menu untuk: </span>
                        <span class="font-mono bg-amber-200 px-2 py-0.5 rounded" x-text="targetAppendOrder?.table_number ? 'Meja ' + targetAppendOrder.table_number : targetAppendOrder?.invoice_number"></span>
                        <span class="text-[11px] font-normal text-amber-800 ml-1">(Menu yang dipilih akan langsung digabung ke nota ini)</span>
                    </div>
                    <button @click="targetAppendOrder = null" class="text-rose-700 hover:underline text-[11px]">&times; Batalkan Mode Tambah</button>
                </div>

                {{-- Grid Menu Kartu Kopi --}}
                <div class="flex-1 overflow-y-auto p-3 sm:p-5 no-scrollbar bg-[#F9F6F0]">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2.5 sm:gap-4">
                        <template x-for="(product, idx) in filteredProducts" :key="product.key">
                            <div @click="handleProductClick(product)"
                                 class="group bg-[#FFFDF9] rounded-xl sm:rounded-2xl border border-[#E8DFD8] hover:border-[#8C6239] shadow-sm hover:shadow-md cursor-pointer transition-all duration-200 flex flex-col justify-between overflow-hidden active:scale-95">
                                
                                {{-- Gambar Menu (Label stok dihilangkan) --}}
                                <div class="h-24 sm:h-28 md:h-32 w-full bg-[#EDE4DA] overflow-hidden relative">
                                    <template x-if="product.image">
                                        <img :src="product.image" :alt="product.name" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </template>
                                    <template x-if="!product.image">
                                        <div class="w-full h-full flex flex-col items-center justify-center text-[#B5A496]">
                                            <span class="text-[10px] font-medium tracking-wide">Riak Coffee</span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Informasi Produk --}}
                                <div class="p-2 sm:p-3 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="text-[8px] sm:text-[9px] font-bold text-[#8C6239] uppercase tracking-wider block mb-0.5 truncate" x-text="product.category_name"></span>
                                        <h4 class="font-bold text-[#2B1810] text-[11px] sm:text-xs leading-snug line-clamp-2" x-text="product.name"></h4>
                                    </div>
                                    <div class="mt-2 sm:mt-3 pt-1.5 sm:pt-2 border-t border-[#F3ECE4] flex items-center justify-between">
                                        <span class="font-extrabold text-[11px] sm:text-xs text-[#4A2E1B] font-mono" x-text="formatRupiah(product.price)"></span>
                                        <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-[#4A2E1B] group-hover:bg-[#8C6239] text-white flex items-center justify-center text-[10px] sm:text-xs font-bold transition">
                                            +
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- TOMBOL FLOATING MOBILE --}}
            <div class="lg:hidden fixed bottom-0 inset-x-0 bg-[#FFFDF9] border-t border-[#E8DFD8] p-3 px-4 flex items-center justify-between shadow-2xl z-30">
                <div class="flex items-center gap-2" @click="mobileCartOpen = true">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl bg-[#4A2E1B] text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span x-show="cart.length > 0" class="absolute -top-1.5 -right-1.5 bg-[#9E2A2B] text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white" x-text="cart.reduce((sum, item) => sum + item.quantity, 0)"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-[#7B6E65] block">Total Pesanan:</span>
                        <span class="text-sm font-extrabold text-[#2B1810] font-mono" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>
                <button @click="mobileCartOpen = true" class="py-2.5 px-5 bg-[#4A2E1B] text-white rounded-xl font-bold text-xs shadow">
                    Lihat Pesanan
                </button>
            </div>

            {{-- BACKDROP MOBILE CART --}}
            <div x-show="mobileCartOpen" 
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileCartOpen = false" 
                 class="lg:hidden fixed inset-0 bg-[#2B1810]/60 z-40 backdrop-blur-xs" style="display: none;"></div>

            {{-- PANEL KERANJANG & CHECKOUT --}}
            <div :class="mobileCartOpen ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'"
                 class="fixed lg:static bottom-0 inset-x-0 z-40 lg:z-auto h-[88vh] lg:h-full lg:w-96 bg-[#FFFDF9] flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-[#E8DFD8] shadow-2xl lg:shadow-lg rounded-t-3xl lg:rounded-none transition-transform duration-300 ease-out">
                
                {{-- Header Keranjang --}}
                <div class="p-3.5 sm:p-4 border-b border-[#E8DFD8] flex justify-between items-center bg-[#FDFBF7] rounded-t-3xl lg:rounded-none">
                    <div>
                        <h3 class="font-serif font-bold text-[#2B1810] text-sm tracking-wide" x-text="targetAppendOrder ? 'Tambah ke ' + (targetAppendOrder.table_number ? 'Meja ' + targetAppendOrder.table_number : targetAppendOrder.invoice_number) : 'Pesanan Kasir'"></h3>
                        <span class="text-[10px] text-[#7B6E65]" x-text="cart.length + ' item dipilih'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="cart = []; targetAppendOrder = null;" x-show="cart.length > 0" class="text-[#9E2A2B] text-xs hover:underline font-semibold transition">
                            Reset
                        </button>
                        <button @click="mobileCartOpen = false" class="lg:hidden text-[#7B6E65] hover:text-[#2B1810] p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Item Keranjang --}}
                <div class="flex-1 overflow-y-auto p-3.5 sm:p-4 divide-y divide-[#F3ECE4] no-scrollbar">
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-[#A6978A] py-10">
                            <div class="w-14 h-14 rounded-full bg-[#F5EFEB] flex items-center justify-center mb-2.5">
                                <svg class="w-7 h-7 text-[#C2B4A7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium">Belum ada item pesanan</span>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="item.cart_key">
                        <div class="py-2.5 flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <h5 class="text-xs font-bold text-[#2B1810] truncate" x-text="item.name"></h5>
                                    <template x-if="item.temperature">
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold tracking-wider uppercase shrink-0"
                                              :class="item.temperature === 'hot' ? 'bg-orange-100 text-orange-800' : 'bg-cyan-100 text-cyan-800'"
                                              x-text="item.temperature.toUpperCase()"></span>
                                    </template>
                                </div>
                                <span class="text-[10px] text-[#8C6239] font-mono" x-text="formatRupiah(item.price)"></span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="flex items-center border border-[#D7C7B7] rounded-lg bg-[#FDFBF7] overflow-hidden">
                                    <button @click="decreaseQty(index)" class="px-2 py-0.5 text-[#5C4A3E] hover:bg-[#F3ECE4] font-bold text-xs transition">-</button>
                                    <span class="px-2 text-xs font-bold text-[#2B1810] font-mono" x-text="item.quantity"></span>
                                    <button @click="increaseQty(index)" class="px-2 py-0.5 text-[#5C4A3E] hover:bg-[#F3ECE4] font-bold text-xs transition">+</button>
                                </div>
                                <span class="text-xs font-bold text-[#2B1810] w-14 sm:w-16 text-right font-mono" x-text="formatRupiah(item.price * item.quantity)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Kalkulator Pembayaran Kasir --}}
                <div class="p-3.5 sm:p-4 bg-[#F9F6F0] border-t border-[#E8DFD8] space-y-2.5 shrink-0">
                    
                    <div class="flex items-center justify-between gap-2" x-show="!targetAppendOrder">
                        <span class="text-xs font-bold text-[#5C4A3E]">Nomor Meja:</span>
                        <input type="text" x-model="tableNumber" placeholder="Contoh: 05 / Takeaway" class="w-36 py-1 px-2.5 text-xs text-right rounded-lg border-[#D7C7B7] bg-white font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239]">
                    </div>

                    <div class="flex justify-between text-xs text-[#5C4A3E]">
                        <span>Subtotal:</span>
                        <span class="font-mono font-semibold text-[#2B1810]" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    <div class="flex items-center justify-between gap-2" x-show="!targetAppendOrder">
                        <span class="text-xs text-[#5C4A3E]">Diskon (Rp):</span>
                        <input type="number" x-model.number="discountAmount" min="0" placeholder="0" class="w-24 sm:w-28 py-1 px-2.5 text-xs text-right rounded-lg border-[#D7C7B7] bg-white font-mono font-bold text-[#9E2A2B] focus:border-[#8C6239] focus:ring-[#8C6239]">
                    </div>

                    <div class="flex items-center justify-between text-xs text-[#5C4A3E]" x-show="!targetAppendOrder">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" x-model="includeTax" class="rounded border-[#D7C7B7] text-[#4A2E1B] focus:ring-[#4A2E1B] text-xs">
                            <span class="ml-1.5">PPN 11%</span>
                        </label>
                        <span class="font-mono font-semibold text-[#2B1810]" x-text="formatRupiah(tax)"></span>
                    </div>

                    <div class="flex justify-between items-center text-sm font-bold text-[#2B1810] pt-2 border-t border-[#E2D6C8]">
                        <span class="font-serif">Total Tagihan:</span>
                        <span class="text-base text-[#4A2E1B] font-mono font-extrabold" x-text="formatRupiah(grandTotal)"></span>
                    </div>

                    <div x-show="!targetAppendOrder">
                        <div class="grid grid-cols-4 gap-1 sm:gap-1.5">
                            <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-[#4A2E1B] text-white shadow-sm' : 'bg-white border border-[#D7C7B7] text-[#5C4A3E]'" class="py-1.5 rounded-lg text-[11px] font-bold transition">Tunai</button>
                            <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-[#4A2E1B] text-white shadow-sm' : 'bg-white border border-[#D7C7B7] text-[#5C4A3E]'" class="py-1.5 rounded-lg text-[11px] font-bold transition">QRIS</button>
                            <button @click="paymentMethod = 'transfer'" :class="paymentMethod === 'transfer' ? 'bg-[#4A2E1B] text-white shadow-sm' : 'bg-white border border-[#D7C7B7] text-[#5C4A3E]'" class="py-1.5 rounded-lg text-[11px] font-bold transition">Transfer</button>
                            <button @click="paymentMethod = 'later'" :class="paymentMethod === 'later' ? 'bg-[#8C6239] text-white shadow-sm' : 'bg-white border border-[#D7C7B7] text-[#8C6239]'" class="py-1.5 rounded-lg text-[11px] font-bold transition">Bayar Nanti</button>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'cash' && !targetAppendOrder" class="space-y-1.5 pt-0.5">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[#8C6239] font-bold text-xs">Rp</span>
                            <input type="number" x-model.number="cashReceived" placeholder="Uang Diterima" class="pl-8 w-full py-1.5 rounded-lg border-[#D7C7B7] bg-white text-xs font-mono font-bold text-[#2B1810] focus:border-[#8C6239] focus:ring-[#8C6239]">
                        </div>
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-[#5C4A3E]">Kembalian:</span>
                            <span :class="cashChange >= 0 ? 'text-[#2D6A4F]' : 'text-[#9E2A2B]'" class="font-mono" x-text="formatRupiah(cashChange)"></span>
                        </div>
                    </div>

                    <template x-if="!targetAppendOrder">
                        <button @click="processCheckout()" 
                                :disabled="cart.length === 0 || isProcessing || (paymentMethod === 'cash' && cashChange < 0)" 
                                class="w-full py-2.5 sm:py-3 bg-[#4A2E1B] hover:bg-[#382214] disabled:opacity-40 text-white font-bold rounded-xl text-xs tracking-wider uppercase shadow-md transition duration-150">
                            <span x-show="!isProcessing" x-text="paymentMethod === 'later' ? 'Simpan Pesanan (Bayar Nanti)' : 'Bayar & Terbitkan Struk'"></span>
                            <span x-show="isProcessing">Memproses Order...</span>
                        </button>
                    </template>

                    <template x-if="targetAppendOrder">
                        <button @click="executeAppendOrder()" 
                                :disabled="cart.length === 0 || isProcessing" 
                                class="w-full py-2.5 sm:py-3 bg-[#8C6239] hover:bg-[#73502E] disabled:opacity-40 text-white font-bold rounded-xl text-xs tracking-wider uppercase shadow-md transition duration-150">
                            <span x-show="!isProcessing">Tambahkan Pesanan ke Nota Ini</span>
                            <span x-show="isProcessing">Menyimpan Menu Tambahan...</span>
                        </button>
                    </template>
                </div>

            </div>
        </div>
    </div>

    <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    @media print {
        html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        body * { visibility: hidden; }
        #thermal-receipt, #thermal-receipt * { visibility: visible; }
        #thermal-receipt {
            position: absolute; left: 0; top: 0;
            width: 58mm !important; max-width: 58mm !important;
            margin: 0 !important; padding: 2mm !important;
            box-shadow: none !important; border: none !important; border-radius: 0 !important;
            font-family: 'Courier New', Courier, monospace !important; color: #000 !important; line-height: 1.25;
        }
        @page { size: auto; margin: 0mm; }
    }
    </style>

    <script>
        function posApp() {
            return {
                activeShift: @json($activeShift),
                startingCash: 0,
                actualEndingCash: 0,
                showCloseShiftModal: false,
                showHelpModal: false,
                showRecentOrdersModal: false,
                showPendingOrdersModal: false,
                showSettleModal: false,
                completedOrder: null,
                selectedCategory: null,
                searchQuery: '',
                tableNumber: '',
                cart: [],
                discountAmount: 0,
                includeTax: false,
                paymentMethod: 'cash',
                cashReceived: 0,
                isProcessing: false,
                mobileCartOpen: false,

                categories: @json($categories),

                temperatureModal: {
                    open: false,
                    product: null,
                    hotVariant: null,
                    iceVariant: null
                },

                recentOrders: [],
                loadingRecent: false,
                pendingOrders: [],
                pendingCount: 0,
                loadingPending: false,
                targetAppendOrder: null,
                settleTargetOrder: null,
                settlePaymentMethod: 'cash',
                settleCashReceived: 0,

                init() {
                    this.fetchPendingOrders();

                    this.$watch('grandTotal', value => {
                        if (this.paymentMethod === 'cash') {
                            this.cashReceived = value;
                        }
                    });

                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F2' || (e.key === '/' && document.activeElement.tagName !== 'INPUT')) {
                            e.preventDefault();
                            this.$refs.searchInput?.focus();
                        }
                        if (e.key === 'F4') {
                            e.preventDefault();
                            this.paymentMethod = 'cash';
                            this.mobileCartOpen = true;
                            this.cashReceived = this.grandTotal;
                        }
                        if (e.key === 'F8') {
                            e.preventDefault();
                            this.openPendingOrdersModal();
                        }
                        if (e.key === 'F1' || e.key === '?') {
                            if (document.activeElement.tagName !== 'INPUT') {
                                e.preventDefault();
                                this.showHelpModal = !this.showHelpModal;
                            }
                        }
                        if (e.key === 'Escape') {
                            this.searchQuery = '';
                            this.mobileCartOpen = false;
                            this.showHelpModal = false;
                            this.showRecentOrdersModal = false;
                            this.showPendingOrdersModal = false;
                            this.showSettleModal = false;
                            this.temperatureModal.open = false;
                            this.targetAppendOrder = null;
                        }
                    });
                },

                getCategoryCount(catId) {
                    return this.getGroupedProducts(catId).length;
                },

                getGroupedProducts(categoryId = null) {
                    let rawProducts = [];
                    this.categories.forEach(cat => {
                        if (categoryId === null || cat.id === categoryId) {
                            (cat.products || []).forEach(prod => {
                                rawProducts.push({ 
                                    ...prod, 
                                    category_name: cat.name 
                                });
                            });
                        }
                    });

                    const map = {};

                    rawProducts.forEach(prod => {
                        const rawName = (prod.name || '').trim();
                        const match = rawName.match(/^(.*?)\s*[\(\[]\s*(Hot|Ice)\s*[\)\]]$/i);

                        if (match) {
                            const cleanName = match[1].trim();
                            const type = match[2].toLowerCase();

                            if (!map[cleanName]) {
                                map[cleanName] = {
                                    key: 'grp_' + cleanName.replace(/\s+/g, '_').toLowerCase(),
                                    id: prod.id,
                                    name: cleanName,
                                    price: prod.selling_price,
                                    stock: prod.stock,
                                    image: prod.image,
                                    category_name: prod.category_name,
                                    category_id: prod.category_id,
                                    has_variants: true,
                                    hotVariant: null,
                                    iceVariant: null
                                };
                            } else {
                                map[cleanName].stock += prod.stock;
                            }

                            const variantData = {
                                id: prod.id,
                                name: prod.name,
                                price: prod.selling_price,
                                stock: prod.stock
                            };

                            if (type === 'hot') {
                                map[cleanName].hotVariant = variantData;
                            } else if (type === 'ice') {
                                map[cleanName].iceVariant = variantData;
                            }
                        } else {
                            map[rawName] = {
                                key: 'prod_' + prod.id,
                                id: prod.id,
                                name: rawName,
                                price: prod.selling_price,
                                stock: prod.stock,
                                image: prod.image,
                                category_name: prod.category_name,
                                category_id: prod.category_id,
                                has_variants: false,
                                hotVariant: null,
                                iceVariant: null
                            };
                        }
                    });

                    return Object.values(map);
                },

                get filteredProducts() {
                    const list = this.getGroupedProducts(this.selectedCategory);
                    if (!this.searchQuery) return list;
                    return list.filter(p => p.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                },

                handleProductClick(product) {
                    const item = JSON.parse(JSON.stringify(product));

                    if (item.has_variants && (item.hotVariant || item.iceVariant)) {
                        this.temperatureModal.product = item;
                        this.temperatureModal.hotVariant = item.hotVariant;
                        this.temperatureModal.iceVariant = item.iceVariant;
                        this.temperatureModal.open = true;
                    } else {
                        this.pushToCart(item.id, item.name, item.price, item.stock, null);
                    }
                },

                confirmAddToCart(temperature) {
                    const variant = temperature === 'hot' 
                        ? this.temperatureModal.hotVariant 
                        : this.temperatureModal.iceVariant;

                    if (!variant) return;

                    this.pushToCart(
                        variant.id, 
                        this.temperatureModal.product.name, 
                        variant.price, 
                        variant.stock, 
                        temperature
                    );

                    this.temperatureModal.open = false;
                    this.temperatureModal.product = null;
                    this.temperatureModal.hotVariant = null;
                    this.temperatureModal.iceVariant = null;
                },

                pushToCart(id, name, price, stock, temperature) {
                    const cartKey = temperature ? `${id}_${temperature}` : `${id}`;
                    const existing = this.cart.find(item => item.cart_key === cartKey);

                    const totalUsedStock = this.cart
                        .filter(item => item.id === id)
                        .reduce((sum, item) => sum + item.quantity, 0);

                    if (totalUsedStock >= stock) {
                        alert('Batas stok ketersediaan menu tercapai.');
                        return;
                    }

                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cart.push({
                            cart_key: cartKey,
                            id: id,
                            name: name,
                            price: price,
                            quantity: 1,
                            stock: stock,
                            temperature: temperature
                        });
                    }
                },

                increaseQty(index) {
                    const currentItem = this.cart[index];
                    const totalUsedStock = this.cart
                        .filter(item => item.id === currentItem.id)
                        .reduce((sum, item) => sum + item.quantity, 0);

                    if (totalUsedStock < currentItem.stock) {
                        currentItem.quantity++;
                    } else {
                        alert('Batas stok ketersediaan menu tercapai.');
                    }
                },

                decreaseQty(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.cart.splice(index, 1);
                    }
                },

                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
                },

                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get tax() {
                    if (!this.includeTax) return 0;
                    const taxable = Math.max(0, this.subtotal - (this.discountAmount || 0));
                    return Math.round(taxable * 0.11);
                },

                get grandTotal() {
                    const taxable = Math.max(0, this.subtotal - (this.discountAmount || 0));
                    return taxable + this.tax;
                },

                get cashChange() {
                    return (this.cashReceived || 0) - this.grandTotal;
                },

                async openShift() {
                    try {
                        const res = await fetch('{{ route('pos.shift.open') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ starting_cash: this.startingCash })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.activeShift = data.shift;
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Terjadi kendala saat membuka shift.');
                    }
                },

                async closeShift() {
                    try {
                        const res = await fetch('{{ route('pos.shift.close') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ actual_ending_cash: this.actualEndingCash })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            alert('Shift berhasil ditutup. Selisih kas fisik: ' + this.formatRupiah(data.shift.difference));
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Terjadi kendala saat menutup shift.');
                    }
                },

                async fetchPendingOrders() {
                    try {
                        const res = await fetch('{{ route('pos.pending-orders') }}');
                        if (res.ok) {
                            this.pendingOrders = await res.json();
                            this.pendingCount = this.pendingOrders.length;
                        }
                    } catch (e) {
                        console.error('Gagal mengambil antrean pending:', e);
                    }
                },

                async openPendingOrdersModal() {
                    this.showPendingOrdersModal = true;
                    this.loadingPending = true;
                    await this.fetchPendingOrders();
                    this.loadingPending = false;
                },

                async openRecentOrdersModal() {
                    this.showRecentOrdersModal = true;
                    this.loadingRecent = true;
                    try {
                        const res = await fetch('{{ route('pos.recent-orders') }}');
                        if (res.ok) {
                            this.recentOrders = await res.json();
                        }
                    } catch (e) {
                        alert('Gagal mengambil riwayat nota.');
                    } finally {
                        this.loadingRecent = false;
                    }
                },

                async processCheckout() {
                    this.isProcessing = true;
                    try {
                        const payload = {
                            payment_method: this.paymentMethod,
                            table_number: this.tableNumber || '-',
                            discount: this.discountAmount || 0,
                            include_tax: this.includeTax,
                            cash_received: this.paymentMethod === 'cash' ? this.cashReceived : this.grandTotal,
                            items: this.cart.map(i => ({
                                id: i.id,
                                quantity: i.quantity,
                                temperature: i.temperature,
                                note: i.temperature ? i.temperature.toUpperCase() : null
                            }))
                        };

                        const res = await fetch('{{ route('pos.checkout') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (res.ok) {
                            this.completedOrder = data.order;
                            this.cart = [];
                            this.discountAmount = 0;
                            this.tableNumber = '';
                            this.cashReceived = 0;
                            this.mobileCartOpen = false;
                            this.fetchPendingOrders();
                        } else {
                            alert(data.message || 'Gagal memproses transaksi.');
                        }
                    } catch (e) {
                        alert('Koneksi terputus saat memproses checkout.');
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async executeAppendOrder() {
                    if (!this.targetAppendOrder) return;
                    this.isProcessing = true;
                    try {
                        const res = await fetch('/pos/orders/' + this.targetAppendOrder.id + '/append-items', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ 
                                items: this.cart.map(i => ({ 
                                    id: i.id, 
                                    quantity: i.quantity,
                                    temperature: i.temperature,
                                    note: i.temperature ? i.temperature.toUpperCase() : null
                                })) 
                            })
                        });

                        const data = await res.json();
                        if (res.ok) {
                            alert('Pesanan berhasil ditambahkan ke nota Meja ' + (this.targetAppendOrder.table_number || '-'));
                            this.completedOrder = data.order;
                            this.cart = [];
                            this.targetAppendOrder = null;
                            this.mobileCartOpen = false;
                            this.fetchPendingOrders();
                        } else {
                            alert(data.message || 'Gagal menambahkan pesanan.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan saat menambahkan pesanan.');
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async executeSettleOrder() {
                    if (!this.settleTargetOrder) return;
                    try {
                        const res = await fetch('/pos/orders/' + this.settleTargetOrder.id + '/settle', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                payment_method: this.settlePaymentMethod,
                                cash_received: this.settlePaymentMethod === 'cash' ? this.settleCashReceived : (this.settleTargetOrder?.grand_total || 0)
                            })
                        });

                        const data = await res.json();
                        if (res.ok) {
                            this.showSettleModal = false;
                            this.completedOrder = data.order;
                            this.fetchPendingOrders();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('Terjadi kendala saat memproses pelunasan.');
                    }
                }
            }
        }
    </script>
</x-app-layout>