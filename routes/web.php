<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard')->with('status', 'Email verified successfully!');
})->middleware(['auth'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
	
	Volt::route('/create_wallet', 'wallet')->name('create_wallet');
	
	Volt::route('/purchase_data', 'purchase_data');
});


Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "Successfully connected to the database.";
    } catch (\Exception $e) {
        return "DB connection error: " . $e->getMessage();
    }
});