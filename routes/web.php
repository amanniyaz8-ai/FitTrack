<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ru', 'kk'])) {
        session(['locale' => $locale]);
    }
    return redirect(url()->previous(route('dashboard')));
})->name('lang.switch');

// Admin (owner only — trial_ends_at is NULL)
Route::middleware(['auth', \App\Http\Middleware\AdminOnly::class])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/grant', [AdminController::class, 'grantAccess'])->name('admin.grant');
    Route::post('/users/{user}/revoke', [AdminController::class, 'revokeAccess'])->name('admin.revoke');
});

// Public pages (no auth required)
Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');
Route::get('/oferta', fn() => view('oferta'))->name('oferta');
Route::post('/promo/validate', [PromoCodeController::class, 'validate'])->name('promo.validate');

// Checkout requires auth but not active subscription
Route::middleware('auth')->group(function () {
    Route::get('/pricing/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
});

Route::get('/trial-expired', fn() => view('trial-expired'))->name('trial.expired')->middleware('auth');

Route::middleware(['auth', \App\Http\Middleware\CheckTrial::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics');

    Route::resource('clients', ClientController::class);

    Route::get('/clients/{client}/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/clients/{client}/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::patch('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::get('/packages/{package}/sessions', [PackageController::class, 'sessions'])->name('packages.sessions');
    Route::post('/packages/{package}/sessions', [PackageController::class, 'addSession'])->name('packages.addSession');

    Route::post('/sessions/bulk-update', [SessionController::class, 'bulkUpdate'])->name('sessions.bulkUpdate');
    Route::delete('/sessions/bulk-delete', [SessionController::class, 'bulkDelete'])->name('sessions.bulkDelete');
    Route::patch('/sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
    Route::post('/sessions/{session}/update-package-time', [SessionController::class, 'updatePackageTime'])->name('sessions.updatePackageTime');
    Route::post('/sessions/{session}/reschedule', [SessionController::class, 'reschedule'])->name('sessions.reschedule');
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
