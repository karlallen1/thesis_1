<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Admin\SystemLogsController;

// ======================
// USER ROUTES - ONLINE (Pre-registration)
// ======================
Route::middleware(['web'])->group(function () {
    // Landing and Services
    Route::get('/', fn() => view('user.online.landing'))->name('user.landing');
    Route::get('/services', fn() => view('user.online.services'))->name('user.services');

    // Requirements pages
    Route::view('/requirements/tax-declaration', 'user.online.requirements.tax-declaration-requirements')->name('req.tax-declaration');
    Route::view('/requirements/no-improvement', 'user.online.requirements.no-improvement-requirements')->name('req.no-improvement');
    Route::view('/requirements/property-holdings', 'user.online.requirements.property-holdings-requirements')->name('req.property-holdings');
    Route::view('/requirements/non-property-holdings', 'user.online.requirements.non-property-holdings-requirements')->name('req.non-property-holdings');

    // Application Forms
    Route::view('/forms/tax-declaration-form', 'user.online.forms.tax-declaration-form')->name('form.tax-declaration');
    Route::view('/forms/no-improvement-form', 'user.online.forms.no-improvement-form')->name('form.no-improvement');
    Route::view('/forms/property-holdings-form', 'user.online.forms.property-holdings-form')->name('form.property-holdings');
    Route::view('/forms/non-property-holdings-form', 'user.online.forms.non-property-holdings-form')->name('form.non-property-holdings');

    // Form submission
    Route::post('/application/store', [ApplicationController::class, 'store'])->name('application.store');

    // QR Scan Entry – Process token
    Route::get('/queue/scan', [QueueController::class, 'enterViaQR'])->name('queue.scan');

    // QR Scanner Input Page (for hardware scanners)
    Route::get('/scan-qr', function () {
        return view('user.online.scan-qr');
    })->name('scan.qr');

    // ADD THESE NEW ROUTES FOR THE MISSING FUNCTIONALITY
    // API endpoint for QR scanner (returns JSON)
Route::post('/api/scan-qr', [QueueController::class, 'handleScanAjax'])->name('api.scan.qr');

});

// ======================
// USER ROUTES - KIOSK (Walk-in direct entry)
// ======================
Route::middleware(['web'])->group(function () {
    // Kiosk service selection
    Route::get('/kiosk', fn() => view('user.kiosk.kioskservices'))->name('kioskservices');

    // Kiosk Requirements
    Route::get('/kiosk/requirements/tax-declaration', fn() => view('user.kiosk.requirements.taxdeclaration-kioskrequirements'))->name('kiosk.req.tax-declaration');
    Route::get('/kiosk/requirements/no-improvement', fn() => view('user.kiosk.requirements.noimprovement-kioskrequirements'))->name('kiosk.req.no-improvement');
    Route::get('/kiosk/requirements/property-holdings', fn() => view('user.kiosk.requirements.property-holdings-kioskrequirements'))->name('kiosk.req.property-holdings');
    Route::get('/kiosk/requirements/non-property-holdings', fn() => view('user.kiosk.requirements.nonproperty-holdings-kioskrequirements'))->name('kiosk.req.non-property-holdings');

    // Kiosk Forms
    Route::get('/kiosk/forms/tax-declaration', function () {
        $serviceType = request('service_type', 'Tax Declaration');
        return view('user.kiosk.forms.taxdeclaration-kioskform', compact('serviceType'));
    })->name('kiosk.form.tax-declaration');

    Route::get('/kiosk/forms/no-improvement', function () {
        $serviceType = request('service_type', 'No Improvement');
        return view('user.kiosk.forms.noimprovement-kioskform', compact('serviceType'));
    })->name('kiosk.form.no-improvement');

    Route::get('/kiosk/forms/property-holdings', function () {
        $serviceType = request('service_type', 'Property Holdings');
        return view('user.kiosk.forms.property-holdings-kioskform', compact('serviceType'));
    })->name('kiosk.form.property-holdings');

    Route::get('/kiosk/forms/non-property-holdings', function () {
        $serviceType = request('service_type', 'Non-Property Holdings');
        return view('user.kiosk.forms.nonproperty-holdings-kioskform', compact('serviceType'));
    })->name('kiosk.form.non-property-holdings');

    // Legacy redirect
    Route::get('/taxdeclaration-kioskform', function () {
        return redirect()->route('kiosk.form.tax-declaration', request()->query());
    });

    // Kiosk form submission
    Route::post('/queue/kiosk', [QueueController::class, 'store'])->name('queue.kiosk');
});

// ======================
// ADMIN ROUTES - NEW FLOW
// ======================

// 🌐 Public Admin Tab (Landing Page)
Route::get('/admin', function () {
    return view('admin.admin-tab');
})->name('admin.home');

// 🔐 Admin Login & Auth
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // POST logout only
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('admin.auth')
        ->name('admin.logout');
});

// ======================
// PROTECTED ADMIN ROUTES
// ======================
Route::middleware(['admin.auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard redirect
    Route::get('/dashboard', function () {
        $role = session('role');
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard-main');
        } elseif ($role === 'staff') {
            return redirect()->route('admin.dashboard-staff');
        }
        return redirect()->route('admin.home');
    })->name('dashboard');

    // Dashboards
    Route::get('/dashboard-main', [DashboardController::class, 'index'])->name('dashboard-main');
    Route::get('/dashboard-staff', [DashboardController::class, 'index'])->name('dashboard-staff');
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // Queue Status
    Route::get('/queuestatus', function () {
        return view('admin.queuestatus');
    })->name('queuestatus');

    // Queue APIs
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    Route::post('/queue/next', [QueueController::class, 'next'])->name('queue.next');
    Route::post('/queue/{id}/complete', [QueueController::class, 'complete']);
    Route::post('/queue/{id}/cancel', [QueueController::class, 'cancel']);
    Route::post('/queue/{id}/requeue', [QueueController::class, 'requeue']);
    Route::post('/queue/complete-now', [QueueController::class, 'completeNow']);
    Route::post('/queue/cancel-now', [QueueController::class, 'cancelNow']);
    Route::post('/queue/requeue-now', [QueueController::class, 'requeueNow']);

    // 🔒 ADMIN ONLY (formerly main_admin)
    Route::middleware(['admin.auth:admin'])->group(function () {
        Route::get('/usermanagement', function () {
            return view('admin.usermanagement');
        })->name('usermanagement');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('users.updatePassword');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/systemlogs', function () {
            return view('admin.systemlogs');
        })->name('systemlogs');

        Route::get('/api/system-logs', [SystemLogsController::class, 'getLogs'])->name('systemlogs.api');
    });
});

// 🔧 Staff-specific API (if needed)
Route::middleware(['admin.auth'])->prefix('staff')->group(function () {
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);
});

// ✅ Public Queue Display
Route::get('/queue/display', function () {
    return view('queue.display');
})->name('queue.display');

Route::get('/queue/expired', function () {
    return view('queue.expired');
})->name('queue.expired');

Route::get('/queue/data', [QueueController::class, 'displayData'])->name('queue.display-data');

// REMOVED THE OLD DUPLICATE ROUTE - NOW HANDLED IN QueueController
// Route::get('/ticket/print/{id}', function ($id) { ... })

// Show welcome screen after QR scan
Route::get('/user/online/welcome/{id}', [QueueController::class, 'showWelcome'])->name('user.online.welcome');

// Print ticket (for auto-print)
Route::get('/user/online/queue-ticket/{id}', [QueueController::class, 'printTicket'])->name('user.online.queue-ticket');

// Remove any duplicate /ticket/print routes

Route::get('/ticket/print/{id}', function ($id) {
    try {
        $application = \App\Models\Application::findOrFail($id);
        return view('user.online.queue-ticket', compact('application'));
    } catch (\Exception $e) {
        return response('Application not found.', 404);
    }
})->name('ticket.print');

Route::get('/user/online/queue-ticket/{id}', [QueueController::class, 'printTicket'])->name('user.online.queue-ticket');