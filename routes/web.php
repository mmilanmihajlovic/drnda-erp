<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\ItemController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\VehicleController;
use App\Http\Controllers\Settings\WorkerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('podesavanja')->name('settings.')->group(function () {
        Route::get('/', fn () => view('settings.index'))->name('index');
        Route::middleware('role:administrator')->group(function () {
            Route::resource('korisnici', UserController::class)->parameters(['korisnici' => 'user'])
                ->names(['index' => 'users.index', 'create' => 'users.create', 'store' => 'users.store',
                          'show' => 'users.show', 'edit' => 'users.edit', 'update' => 'users.update',
                          'destroy' => 'users.destroy']);
        });
        Route::middleware('role:administrator|menadzment')->group(function () {
            Route::resource('radnici', WorkerController::class)->parameters(['radnici' => 'worker'])
                ->names(['index' => 'workers.index', 'create' => 'workers.create', 'store' => 'workers.store',
                          'show' => 'workers.show', 'edit' => 'workers.edit', 'update' => 'workers.update',
                          'destroy' => 'workers.destroy']);
            Route::resource('vozila', VehicleController::class)->parameters(['vozila' => 'vehicle'])
                ->names(['index' => 'vehicles.index', 'create' => 'vehicles.create', 'store' => 'vehicles.store',
                          'show' => 'vehicles.show', 'edit' => 'vehicles.edit', 'update' => 'vehicles.update',
                          'destroy' => 'vehicles.destroy']);
            Route::resource('artikli', ItemController::class)->parameters(['artikli' => 'item'])
                ->names(['index' => 'items.index', 'create' => 'items.create', 'store' => 'items.store',
                          'show' => 'items.show', 'edit' => 'items.edit', 'update' => 'items.update',
                          'destroy' => 'items.destroy']);
        });
    });
});
require __DIR__.'/auth.php';
