<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-serif font-bold text-xl text-[#2B1810] leading-tight">
                    {{ __('Audit & Rekonsiliasi Shift Kasir') }}
                </h2>
                <p class="text-xs text-[#7B6E65] mt-0.5">Histori laci kasir, serah terima modal, total penjualan tunai, dan selisih fisik.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F9F6F0] min-h-[calc(100vh-65px)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFFDF9] rounded-2xl border border-[#E8DFD8] shadow-sm overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#E8DFD8] bg-[#F5EFEB] text-[11px] uppercase text-[#5C4A3E] font-bold tracking-wider">
                                <th class="py-3 px-4">Kasir</th>
                                <th class="py-3 px-4">Waktu Buka</th>
                                <th class="py-3 px-4">Waktu Tutup</th>
                                <th class="py-3 px-4 text-right">Modal Awal</th>
                                <th class="py-3 px-4 text-right">Penjualan Tunai</th>
                                <th class="py-3 px-4 text-right">Kas Fisik</th>
                                <th class="py-3 px-4 text-center">Selisih</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3ECE4]">
                            @forelse ($shifts as $shift)
                                <tr class="hover:bg-[#FDFBF7] transition">
                                    <td class="py-3 px-4 font-bold text-[#2B1810]">
                                        {{ $shift->user->name ?? 'Kasir' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[#7B6E65]">
                                        {{ $shift->opened_at ? $shift->opened_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[#7B6E65]">
                                        {{ $shift->closed_at ? $shift->closed_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-[#5C4A3E]">
                                        Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-[#2B1810]">
                                        Rp {{ number_format($shift->cash_sales_sum ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-[#2B1810]">
                                        @if ($shift->actual_ending_cash !== null)
                                            Rp {{ number_format($shift->actual_ending_cash, 0, ',', '.') }}
                                        @else
                                            <span class="text-[#A6978A]">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-xs font-bold">
                                        @if ($shift->difference === null)
                                            <span class="text-[#A6978A]">-</span>
                                        @elseif ($shift->difference === 0)
                                            <span class="text-[#2B8A3E] bg-[#EBFBEE] px-2 py-0.5 rounded-md">Pas (0)</span>
                                        @elseif ($shift->difference > 0)
                                            <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">+Rp {{ number_format($shift->difference, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-[#9E2A2B] bg-[#FFF1F0] px-2 py-0.5 rounded-md">-Rp {{ number_format(abs($shift->difference), 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($shift->closed_at)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F3ECE4] text-[#5C4A3E]">Selesai</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EBFBEE] text-[#2B8A3E] animate-pulse">Sedang Berjalan</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($shift->closed_at)
                                            <a href="{{ route('admin.shifts.print', $shift) }}" target="_blank" class="px-2.5 py-1 bg-[#F3ECE4] hover:bg-[#EAE0D5] text-[#4A2E1B] rounded-lg text-xs font-bold transition">
                                                Cetak Rekap
                                            </a>
                                        @else
                                            <span class="text-[#A6978A]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-[#A6978A]">Belum ada rekonsiliasi shift kasir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>