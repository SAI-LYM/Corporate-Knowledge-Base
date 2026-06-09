<?php

use App\Http\Controllers\Auth\AzureController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

// Landing page for guests; logged-in users go straight to their dashboard hub.
Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : view('home'))->name('home');

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

    // Knowledge Base — all reads filtered by Article::visibleTo($user).
    Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
    Route::get('/knowledge/{slug}', [KnowledgeController::class, 'show'])->name('knowledge.show');

    // Onboarding Hub — the logged-in user's own checklist.
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/{item}/toggle', [OnboardingController::class, 'toggle'])->name('onboarding.toggle');
});
