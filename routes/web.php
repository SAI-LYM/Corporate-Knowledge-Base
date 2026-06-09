<?php

use App\Http\Controllers\Auth\AzureController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

// ---- Guest auth routes -----------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    // Mock driver (local/demo only; controller also hard-blocks non-mock/prod).
    Route::post('/login/mock', [LoginController::class, 'mock'])->name('login.mock');

    // Microsoft Entra ID (Azure AD) OAuth flow.
    Route::get('/auth/azure/redirect', [AzureController::class, 'redirect'])->name('auth.azure.redirect');
    Route::get('/auth/azure/callback', [AzureController::class, 'callback'])->name('auth.azure.callback');
});

// ---- Authenticated routes --------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
