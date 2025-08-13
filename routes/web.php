<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('filament.admin.auth.login'));
    // return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
