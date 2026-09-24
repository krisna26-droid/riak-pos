<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Hapus intended URL lama agar tidak mengarah ke halaman terlarang sebelumnya
        $request->session()->forget('url.intended');

        $user = $request->user();

        if ($user->hasRole('cashier')) {
            return redirect()->route('pos.index');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.reports.index');
        }

        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        // Tutup otomatis shift kasir yang masih menggantung saat logout
        if ($userId) {
            $activeShift = CashierShift::where('user_id', $userId)
                ->whereNull('closed_at')
                ->first();

            if ($activeShift) {
                $cashSales = $activeShift->orders()
                    ->where('payment_method', 'cash')
                    ->where('status', 'paid')
                    ->sum('grand_total');

                $expectedEnding = $activeShift->starting_cash + $cashSales;

                $activeShift->update([
                    'expected_ending_cash' => $expectedEnding,
                    'actual_ending_cash'   => $expectedEnding,
                    'difference'           => 0,
                    'closed_at'            => now(),
                ]);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}