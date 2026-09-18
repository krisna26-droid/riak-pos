<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ShiftReportController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Redirect dashboard sesuai role pengguna
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('cashier')) {
            return redirect()->route('pos.index');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.reports.index');
        }

        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        abort(403, 'Akun Anda belum memiliki role akses.');
    })->name('dashboard');

    // Domain Kasir POS (Cashier, Admin, Super Admin)
    Route::middleware(['role:cashier|admin|super-admin'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/shift/open', [PosController::class, 'openShift'])->name('shift.open');
        Route::post('/shift/close', [PosController::class, 'closeShift'])->name('shift.close');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        
        // Fitur Riwayat Nota & Bayar Nanti (Open Bill)
        Route::get('/recent-orders', [PosController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/pending-orders', [PosController::class, 'getPendingOrders'])->name('pending-orders');
        Route::post('/orders/{order}/append-items', [PosController::class, 'appendItems'])->name('orders.append-items');
        Route::post('/orders/{order}/settle', [PosController::class, 'settlePendingOrder'])->name('orders.settle');
    });

    // Domain Operasional Admin & Super Admin
    Route::middleware(['role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/quick-restock', [ProductController::class, 'quickRestock'])->name('products.quick-restock');
        
        // Rute Finansial, Riwayat Order, & Ekspor CSV
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
        Route::post('orders/{order}/void', [ReportController::class, 'voidOrder'])->name('orders.void');
        Route::get('orders/{order}/json', [ReportController::class, 'showJson'])->name('orders.json');
    
        // Rute Audit Shift Kasir & Cetak Rekap
        Route::get('shifts', [ShiftReportController::class, 'index'])->name('shifts.index');
        Route::get('shifts/{shift}/print', [ShiftReportController::class, 'printSummary'])->name('shifts.print');
    });

    // Domain Pengelolaan Akun Staf (Super Admin)
    Route::middleware(['role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Profil Pengguna (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';