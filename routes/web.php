<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShippingLabelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $stats = [
        'total' => auth()->user()->shippingLabels()->count(),
        'active' => auth()->user()->shippingLabels()->where('status', 'created')->count(),
        'cancelled' => auth()->user()->shippingLabels()->where('status', 'cancelled')->count(),
        'total_spent' => auth()->user()->shippingLabels()->where('status', 'created')->sum('rate'),
    ];

    return Inertia::render('Dashboard', [
        'stats' => $stats,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shipping Labels routes
    Route::get('/shipping-labels', [ShippingLabelController::class, 'index'])->name('shipping-labels.index');
    Route::get('/shipping-labels/create', [ShippingLabelController::class, 'create'])->name('shipping-labels.create');
    Route::post('/shipping-labels', [ShippingLabelController::class, 'store'])->name('shipping-labels.store');
    Route::post('/shipping-labels/rates', [ShippingLabelController::class, 'getRates'])->name('shipping-labels.rates');
    Route::get('/shipping-labels/{id}', [ShippingLabelController::class, 'show'])->name('shipping-labels.show');
    Route::delete('/shipping-labels/{id}', [ShippingLabelController::class, 'destroy'])->name('shipping-labels.destroy');
});

require __DIR__.'/auth.php';
