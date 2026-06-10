<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AzureController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProjectController;
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

    // Project Registry — Senior+ only (Freshers get 403); reads still filtered by
    // Project::visibleTo($user) so cross-department/advanced items stay hidden.
    Route::middleware('role:Senior')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
    });

    // ---- Admin area — Admin role only (whole group gated; writes also gated by Policy) --
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manage users: assign role + department.
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        // Full CRUD for Articles and Projects.
        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::resource('projects', AdminProjectController::class)->except(['show']);
    });
});
