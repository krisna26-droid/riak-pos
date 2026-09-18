<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Laporan Finansial & Analitik Riak Coffee') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Rekapitulasi omzet, HPP, laba kotor, dan riwayat transaksi penjualan.</p>
            </div>

            {{-- Filter Rentang Tanggal --}}
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2 text-sm">
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs py-1.5 focus:border-[#8C6239] focus:ring-[#8C6239]">
                <span class="text-[#8C6239] font-bold text-xs">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-xl border-[#D7C7B7] bg-[#FDFBF7] text-xs py-1.5 focus:border-[#8C6239] focus:ring-[#8C6239]">
                <button type="submit" class="px-3 py-1.5 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow transition">
                    Filter
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]" 
        x-data="{ 
            voidModal: false, 
            activeOrderId: null, 
            invoiceNum: '',
            detailModal: false,
            orderDetail: null,
            loadingDetail: false,
            async openDetail(id) {
                this.loadingDetail = true;
                this.detailModal = true;
                try {
                    const res = await fetch('/admin/orders/' + id + '/json');
                    this.orderDetail = await res.json();
                } catch (e) {
                    alert('Gagal mengambil rincian order.');
                    this.detailModal = false;
                } finally {
                    this.loadingDetail = false;
                }
            }
        }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- KARTU KPI FINANSIAL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-[#8C6239] tracking-wider">Total Omzet (Revenue)</span>
                        <div class="text-2xl font-black text-[#2B1810] mt-2 font-mono">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[11px] text-[#7B6E65] mt-3">Pendapatan kotor dari order berstatus paid</span>
                </div>

                <div class="bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-[#8C6239] tracking-wider">Total Modal (HPP)</span>
                        <div class="text-2xl font-black text-[#9E2A2B] mt-2 font-mono">
                            Rp {{ number_format($totalCogs, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[11px] text-[#7B6E65] mt-3">Akumulasi modal dasar bahan/produk terjual</span>
                </div>

                <div class="bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-[#8C6239] tracking-wider">Laba Kotor (Gross Profit)</span>
                        <div class="text-2xl font-black text-[#2B8A3E] mt-2 font-mono">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[11px] text-[#7B6E65] mt-3">Selisih total omzet dikurangi HPP</span>
                </div>

                <div class="bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-[#8C6239] tracking-wider">Volume Transaksi</span>
                        <div class="text-2xl font-black text-[#2B1810] mt-2 font-mono">
                            {{ number_format($totalOrders, 0, ',', '.') }} <span class="text-xs font-normal text-[#7B6E65]">Struk</span>
                        </div>
                    </div>
                    <span class="text-[11px] text-[#7B6E65] mt-3">Jumlah pesanan yang berhasil diproses</span>
                </div>
            </div>

            {{-- 5 MENU TERLARIS & METODE PEMBAYARAN --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- 5 Menu Terlaris --}}
                <div class="lg:col-span-2 bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xs font-bold text-[#2B1810] uppercase tracking-wider">5 Menu Terlaris (Top Selling)</h3>
                        <span class="text-[11px] text-[#7B6E65]">Berdasarkan kuantitas terjual</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[10px] uppercase text-[#5C4A3E] font-bold">
                                    <th class="py-2.5 px-3">Rank</th>
                                    <th class="py-2.5 px-3">Nama Menu</th>
                                    <th class="py-2.5 px-3">Kategori</th>
                                    <th class="py-2.5 px-3 text-center">Terjual</th>
                                    <th class="py-2.5 px-3 text-right">Akumulasi Penjualan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3ECE4]">
                                @forelse ($topProducts as $index => $item)
                                    <tr class="hover:bg-[#FDFBF7] transition">
                                        <td class="py-2.5 px-3 font-bold text-[#8C6239]">
                                            #{{ $index + 1 }}
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold text-[#2B1810]">
                                            {{ $item->product->name ?? 'Menu Terhapus' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-[#7B6E65]">
                                            {{ $item->product->category->name ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#F3ECE4] text-[#4A2E1B]">
                                                {{ $item->total_qty }} porsi
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-[#2B1810]">
                                            Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-xs text-[#A6978A]">Belum ada item terjual pada rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Distribusi Metode Pembayaran --}}
                <div class="bg-[#FFFDF9] p-5 rounded-2xl border border-[#E8DFD8] shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-[#2B1810] uppercase tracking-wider mb-4">Metode Pembayaran</h3>
                        <div class="space-y-3">
                            @forelse ($paymentMethods as $pm)
                                <div class="p-3 rounded-xl bg-[#FDFBF7] border border-[#E8DFD8]">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold uppercase text-[#8C6239] font-mono">{{ strtoupper($pm->payment_method) }}</span>
                                        <span class="text-[11px] px-2 py-0.5 rounded-md bg-[#F3ECE4] text-[#4A2E1B] font-bold">{{ $pm->count }} trx</span>
                                    </div>
                                    <div class="text-base font-bold text-[#2B1810] mt-1 font-mono">
                                        Rp {{ number_format($pm->total, 0, ',', '.') }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-[#A6978A]">Belum ada transaksi pada periode ini.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="text-[10px] text-[#A6978A] mt-4 pt-3 border-t border-[#F3ECE4] text-center">
                        Total metode transaksi terekam otomatis
                    </div>
                </div>
            </div>

            {{-- TABEL RIWAYAT TRANSAKSI --}}
            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm overflow-hidden">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <h3 class="font-serif font-bold text-base text-[#2B1810]">Riwayat Transaksi Penjualan</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-3 py-1.5 bg-[#2B8A3E] hover:bg-[#237032] text-white rounded-xl text-xs font-bold shadow transition">
                                <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Ekspor CSV
                            </a>
                            <button onclick="window.print()" class="px-3 py-1.5 border border-[#D7C7B7] hover:bg-[#F3ECE4] text-[#5C4A3E] rounded-xl text-xs font-bold transition">
                                Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[11px] uppercase text-[#5C4A3E] font-bold tracking-wider">
                                    <th class="py-3 px-4">No. Invoice</th>
                                    <th class="py-3 px-4">Waktu</th>
                                    <th class="py-3 px-4">Kasir</th>
                                    <th class="py-3 px-4 text-center">Meja</th>
                                    <th class="py-3 px-4">Rincian Menu</th>
                                    <th class="py-3 px-4 text-center">Metode</th>
                                    <th class="py-3 px-4 text-right">Total Tagihan</th>
                                    <th class="py-3 px-4 text-center">Aksi / Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3ECE4]">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-[#FDFBF7] transition">
                                        <td class="py-3 px-4 font-mono font-bold text-[#2B1810]">
                                            {{ $order->invoice_number }}
                                        </td>
                                        <td class="py-3 px-4 text-[#7B6E65] whitespace-nowrap font-mono">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-[#5C4A3E]">
                                            {{ $order->cashier->name ?? 'Kasir' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($order->table_number)
                                                <span class="px-2 py-0.5 rounded-md bg-[#F3ECE4] text-[#4A2E1B] font-bold text-[11px]">
                                                    {{ $order->table_number }}
                                                </span>
                                            @else
                                                <span class="text-[#A6978A] italic text-[11px]">Takeaway</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-[#5C4A3E]">
                                            <ul class="list-disc list-inside space-y-0.5">
                                                @foreach ($order->items as $item)
                                                    <li>{{ $item->product->name ?? 'Menu' }} <span class="font-mono text-[10px] text-[#A6978A]">({{ $item->quantity }}x)</span></li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                                {{ $order->payment_method === 'cash' ? 'bg-[#EBFBEE] text-[#2B8A3E]' : ($order->payment_method === 'qris' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700') }}">
                                                {{ $order->payment_method }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-[#4A2E1B] whitespace-nowrap text-sm">
                                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center space-x-1.5 whitespace-nowrap">
                                            <button @click="openDetail({{ $order->id }})" class="px-2.5 py-1 text-[11px] font-bold text-[#8C6239] border border-[#D7C7B7] hover:bg-[#F3ECE4] rounded-lg transition">
                                                Lihat Struk
                                            </button>
                                            @if ($order->status === 'paid')
                                                <button @click="activeOrderId = {{ $order->id }}; invoiceNum = '{{ $order->invoice_number }}'; voidModal = true" class="px-2.5 py-1 text-[11px] font-bold text-[#9E2A2B] border border-[#F5C2C0] hover:bg-[#FFF1F0] rounded-lg transition">
                                                    Void
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF1F0] text-[#9E2A2B]" title="{{ $order->cancellation_reason }}">
                                                    Dibatalkan
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-8 text-[#A6978A]">
                                            Tidak ada data transaksi yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Modal Detail Struk & Cetak Ulang --}}
        <template x-if="detailModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4">
                <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-sm w-full p-5 text-center my-auto">
                    
                    <div x-show="loadingDetail" class="py-8 text-xs text-[#7B6E65]">
                        Memuat data struk...
                    </div>

                    <div x-show="!loadingDetail && orderDetail">
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

                            <div class="text-[11px] space-y-0.5">
                                <div class="flex justify-between">
                                    <span>No. Inv:</span>
                                    <span class="font-bold" x-text="orderDetail?.invoice_number"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Waktu:</span>
                                    <span x-text="orderDetail?.created_at"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Kasir:</span>
                                    <span x-text="orderDetail?.cashier_name"></span>
                                </div>
                                <div class="flex justify-between" x-show="orderDetail?.table_number">
                                    <span>Meja:</span>
                                    <span class="font-bold" x-text="orderDetail?.table_number"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Metode:</span>
                                    <span class="font-bold uppercase" x-text="orderDetail?.payment_method"></span>
                                </div>
                            </div>

                            <div class="border-t border-dashed border-stone-400 my-2"></div>

                            <div class="space-y-1.5 text-[11px]">
                                <template x-for="item in orderDetail?.items" :key="item.name">
                                    <div>
                                        <div class="font-semibold text-stone-900" x-text="item.name"></div>
                                        <div class="flex justify-between text-stone-600 pl-2">
                                            <span x-text="item.quantity + ' x Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                            <span class="font-mono text-stone-900" x-text="'Rp ' + Number(item.subtotal).toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t border-dashed border-stone-400 my-2"></div>

                            <div class="space-y-1 text-[11px]">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-mono" x-text="'Rp ' + Number(orderDetail?.subtotal || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-rose-600" x-show="orderDetail?.discount > 0">
                                    <span>Diskon</span>
                                    <span class="font-mono" x-text="'-Rp ' + Number(orderDetail?.discount || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between" x-show="orderDetail?.tax > 0">
                                    <span>PPN (11%)</span>
                                    <span class="font-mono" x-text="'Rp ' + Number(orderDetail?.tax || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between font-extrabold text-xs pt-1 border-t border-dotted border-stone-400">
                                    <span>TOTAL TAGIHAN</span>
                                    <span class="font-mono text-sm" x-text="'Rp ' + Number(orderDetail?.grand_total || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-[11px]" x-show="orderDetail?.payment_method === 'CASH'">
                                    <span>Tunai Diterima</span>
                                    <span class="font-mono" x-text="'Rp ' + Number(orderDetail?.cash_received || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-[11px]" x-show="orderDetail?.payment_method === 'CASH'">
                                    <span>Kembalian</span>
                                    <span class="font-mono" x-text="'Rp ' + Number(orderDetail?.cash_change || 0).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div class="border-t border-dashed border-stone-400 my-3"></div>

                            <div class="text-center text-[10px] text-stone-600 space-y-0.5 pt-1">
                                <p class="font-semibold">SALINAN STRUK (RE-PRINT)</p>
                                <p>Riak Coffee &bull; Coffee &amp; Lake</p>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button onclick="window.print()" class="flex-1 py-2.5 bg-[#4A2E1B] hover:bg-[#382214] text-white rounded-xl text-xs font-bold shadow transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak Salinan
                            </button>
                            <button @click="detailModal = false" class="flex-1 py-2.5 bg-[#F3ECE4] hover:bg-[#EAE0D5] text-[#5C4A3E] rounded-xl text-xs font-semibold">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Konfirmasi Void --}}
        <template x-if="voidModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-[#2B1810]/70 backdrop-blur-sm p-4">
                <div class="bg-[#FFFDF9] rounded-2xl shadow-2xl border border-[#E8DFD8] max-w-md w-full p-6">
                    <div class="w-12 h-12 rounded-full bg-[#FFF1F0] text-[#9E2A2B] flex items-center justify-center mb-3 border border-[#F5C2C0]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-serif font-bold text-lg text-[#2B1810]">Batalkan Transaksi (Void)?</h3>
                    <p class="text-xs text-[#7B6E65] mt-1">Invoice: <span class="font-mono font-bold text-[#2B1810]" x-text="invoiceNum"></span></p>
                    <p class="text-xs text-[#9E2A2B] mt-1 font-semibold">Perhatian: Seluruh stok dari menu dalam transaksi ini akan otomatis dikembalikan ke inventaris.</p>

                    <form :action="'/admin/orders/' + activeOrderId + '/void'" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold text-[#5C4A3E] uppercase tracking-wider mb-1">Alasan Pembatalan *</label>
                            <textarea name="reason" rows="2" required placeholder="Contoh: Salah input pesanan / mesin kopi kendala" class="w-full text-xs rounded-xl border-[#D7C7B7] bg-[#FDFBF7] focus:border-[#8C6239] focus:ring-[#8C6239]"></textarea>
                        </div>

                        <div class="flex gap-2.5">
                            <button type="button" @click="voidModal = false" class="flex-1 py-2.5 text-xs font-semibold text-[#5C4A3E] border border-[#D7C7B7] rounded-xl hover:bg-[#F3ECE4] transition">Batal</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-bold text-white bg-[#9E2A2B] hover:bg-[#852324] rounded-xl shadow transition">Konfirmasi Void</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>