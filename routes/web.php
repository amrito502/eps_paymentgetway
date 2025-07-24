<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EpsPaymentController;


Route::get('/', function () {
    return view('welcome');
});


// Route::get('/pay', [EpsPaymentController::class, 'initiatePayment'])->name('eps.pay');
// Route::post('/eps/success', [EpsPaymentController::class, 'paymentSuccess'])->name('eps.success');
// Route::post('/eps/fail', [EpsPaymentController::class, 'paymentFail'])->name('eps.fail');


Route::prefix('eps')->group(function () {
    Route::get('/', [EpsController::class, 'showPaymentPage'])->name('eps.payment');
    Route::get('/pay', [EpsController::class, 'initiatePayment'])->name('eps.initiate');
    Route::get('/callback', [EpsController::class, 'handleCallback'])->name('eps.callback');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::post('/track-location', [LocationController::class, 'store']);
    Route::get('/realtime-location', [LocationController::class, 'showRealtime'])->name('realtime.location');
    Route::get('/get-location', [LocationController::class, 'getLocation']);
});

// routes/web.php


require __DIR__.'/auth.php';
