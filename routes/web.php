<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;


// ======================
// USER ROUTES
// ======================
Route::middleware(['web'])->group(function () {
    // Paste all your routes here

Route::get('/', fn () => view('user.landing'));

Route::get('/services', fn () => view('user.services'))->name('user.services');

// ✅ Requirements pages
Route::view('/requirements/tax-declaration', 'user.requirements.tax-declaration')->name('req.tax-declaration');
Route::view('/requirements/no-improvement', 'user.requirements.no-improvement')->name('req.no-improvement');
Route::view('/requirements/property-holdings', 'user.requirements.property-holdings')->name('req.property-holdings');
Route::view('/requirements/non-property-holdings', 'user.requirements.non-property-holdings')->name('req.non-property-holdings');

// ✅ Application Forms
Route::view('/forms/tax-declaration-form', 'user.forms.tax-declaration-form')->name('form.tax-declaration');
Route::view('/forms/no-improvement-form', 'user.forms.no-improvement-form')->name('form.no-improvement');
Route::view('/forms/property-holdings-form', 'user.forms.property-holdings-form')->name('form.property-holdings');
Route::view('/forms/non-property-holdings-form', 'user.forms.non-property-holdings-form')->name('form.non-property-holdings');

// ✅ Form submission
Route::post('/application/store', [ApplicationController::class, 'store'])->name('application.store');


// ======================
// ADMIN ROUTES
// ======================

// 🔐 Admin Auth
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// 🧭 Admin Dashboard with stats
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// ✅ Admin Panel Page Views (Blade-based UI)
Route::view('/admin/usermanagement', 'admin.usermanagement')->name('admin.usermanagement');
Route::view('/admin/queuestatus', 'admin.queuestatus')->name('admin.queuestatus');
Route::view('/admin/systemlogs', 'admin.systemlogs')->name('admin.systemlogs');

// 🔧 Admin User Management API (JSON only)
Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index'); // JSON list
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('admin.users.updatePassword');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');



// 🔧 Queue Management API
    // Queue APIs
    Route::get('/queue', [QueueController::class, 'index'])->name('admin.queue.index');
    Route::put('/queue/{id}/complete', [QueueController::class, 'complete']);
    Route::put('/queue/{id}/cancel', [QueueController::class, 'cancel']);
    Route::put('/queue/{id}/requeue', [QueueController::class, 'requeue']);
});
});