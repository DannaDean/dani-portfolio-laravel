<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioHomeController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DashboardController;

Route::get('/', PortfolioHomeController::class)->name('home');
Route::post('/contact', [ContactMessageController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/{section}', [DashboardController::class, 'index'])->name('dashboard.section');
    Route::post('dashboard/{section}', [DashboardController::class, 'store'])->name('dashboard.store');
    Route::put('dashboard/{section}/{id}', [DashboardController::class, 'update'])->name('dashboard.update');
    Route::delete('dashboard/{section}/{id}', [DashboardController::class, 'destroy'])->name('dashboard.destroy');
});

require __DIR__.'/auth.php';
