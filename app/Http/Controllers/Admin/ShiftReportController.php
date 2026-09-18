<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShiftReportController extends Controller
{
    /**
     * Menampilkan riwayat rekonsiliasi kas dan status shift kasir.
     */
    public function index(Request $request): View
    {
        $shifts = CashierShift::with(['user:id,name'])
            ->withSum(['orders as cash_sales_sum' => function ($query) {
                $query->where('payment_method', 'cash')->where('status', 'paid');
            }], 'grand_total')
            ->latest('opened_at')
            ->paginate(15);

        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Menampilkan struk ringkasan rekonsiliasi kas saat shift ditutup.
     */
    public function printSummary(CashierShift $shift): View
    {
        $shift->load(['user:id,name']);

        $salesSummary = $shift->orders()
            ->where('status', 'paid')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as trx_count'),
                DB::raw('SUM(grand_total) as total_amount')
            )
            ->groupBy('payment_method')
            ->get();

        return view('admin.shifts.print', compact('shift', 'salesSummary'));
    }
}