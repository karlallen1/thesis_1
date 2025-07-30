    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ApplicationController;
    use App\Http\Controllers\AdminAuthController;
    use App\Http\Controllers\AdminUserController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\QueueController;
    use Illuminate\Support\Facades\Auth;    

    // ======================
    // USER ROUTES - ONLINE (Pre-registration)
    // ======================
    Route::middleware(['web'])->group(function () {

        // Landing and Services
        Route::get('/', fn () => view('user.online.landing'));
        Route::get('/services', fn () => view('user.online.services'))->name('user.services');

        // ✅ Requirements pages - Fixed file names
        Route::view('/requirements/tax-declaration', 'user.online.requirements.tax-declaration-requirements')->name('req.tax-declaration');
        Route::view('/requirements/no-improvement', 'user.online.requirements.no-improvement-requirements')->name('req.no-improvement');
        Route::view('/requirements/property-holdings', 'user.online.requirements.property-holdings-requirements')->name('req.property-holdings');
        Route::view('/requirements/non-property-holdings', 'user.online.requirements.non-property-holdings-requirements')->name('req.non-property-holdings');

        // ✅ Application Forms - Fixed file names
        Route::view('/forms/tax-declaration-form', 'user.online.forms.tax-declaration-form')->name('form.tax-declaration');
        Route::view('/forms/no-improvement-form', 'user.online.forms.no-improvement-form')->name('form.no-improvement');
        Route::view('/forms/property-holdings-form', 'user.online.forms.property-holdings-form')->name('form.property-holdings');
        Route::view('/forms/non-property-holdings-form', 'user.online.forms.non-property-holdings-form')->name('form.non-property-holdings');

        // ✅ Form submission
        Route::post('/application/store', [ApplicationController::class, 'store'])->name('application.store');

        // ✅ SECURE QR SCAN ENTRY - Now uses token parameter
        Route::get('/queue/scan', [QueueController::class, 'enterViaQR'])->name('queue.scan');
    });

    // ======================
    // USER ROUTES - KIOSK (Walk-in direct entry)
    // ======================
    Route::middleware(['web'])->group(function () {
        
        // ✅ Kiosk service selection page
        Route::get('/kiosk', function () {
            return view('user.kiosk.kioskservices');
        })->name('kioskservices');
        
        // ✅ Kiosk Requirements Pages - Fixed paths and file names
        Route::get('/kiosk/requirements/tax-declaration', function () {
            return view('user.kiosk.requirements.taxdeclaration-kioskrequirements');
        })->name('kiosk.req.tax-declaration');
        
        Route::get('/kiosk/requirements/no-improvement', function () {
            return view('user.kiosk.requirements.noimprovement-kioskrequirements');
        })->name('kiosk.req.no-improvement');
        
        Route::get('/kiosk/requirements/property-holdings', function () {
            return view('user.kiosk.requirements.property-holdings-kioskrequirements');
        })->name('kiosk.req.property-holdings');
        
        Route::get('/kiosk/requirements/non-property-holdings', function () {
            return view('user.kiosk.requirements.nonproperty-holdings-kioskrequirements');
        })->name('kiosk.req.non-property-holdings');
        
        // ✅ Kiosk Forms - Fixed paths and file names with dynamic service handling
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
        })->name('kiosk.form.property-holdings-form');
        
        Route::get('/kiosk/forms/non-property-holdings', function () {
            $serviceType = request('service_type', 'Non-Property Holdings');
            return view('user.kiosk.forms.nonproperty-holdings-kioskform', compact('serviceType'));
        })->name('kiosk.form.non-property-holdings');
        
        // ✅ Legacy route for backward compatibility (redirects to specific form)
        Route::get('/taxdeclaration-kioskform', function () {
            return redirect()->route('kiosk.form.tax-declaration', request()->query());
        });
        
        // ✅ Kiosk form submission - direct queue entry
        Route::post('/queue/kiosk', [QueueController::class, 'store'])->name('queue.kiosk');
    });

    // ======================
    // ADMIN ROUTES
    // ======================

    // 🔐 Admin Auth
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
    Route::get('/admin/logout', function () {
        session()->flush();
        return redirect('/admin/login');
    })->name('admin.logout');

    // 🧭 Admin Dashboard with stats - FIXED DASHBOARD ROUTE ONLY
    Route::get('/admin/dashboard-main', [DashboardController::class, 'index'])->name('admin.dashboard-main');
    Route::get('/admin/dashboard-stats', [DashboardController::class, 'getStats'])->name('admin.dashboard.stats');

    // ✅ Admin Panel Page Views (Blade-based UI)
    Route::view('/admin/usermanagement', 'admin.usermanagement')->name('admin.usermanagement');
    Route::view('/admin/queuestatus', 'admin.queuestatus')->name('admin.queuestatus');
    Route::view('/admin/systemlogs', 'admin.systemlogs')->name('admin.systemlogs');
  

    // 🔧 Admin User Management & Queue APIs
    Route::prefix('admin')->middleware('web')->group(function () {
        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('admin.users.updatePassword');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // ✅ Queue Management APIs
        Route::get('/queue', [QueueController::class, 'index'])->name('admin.queue.index');
        Route::post('/queue/next', [QueueController::class, 'next'])->name('admin.queue.next');
        Route::post('/queue/{id}/complete', [QueueController::class, 'complete']);
        Route::post('/queue/{id}/cancel', [QueueController::class, 'cancel']);
        Route::post('/queue/{id}/requeue', [QueueController::class, 'requeue']);
        Route::post('/queue/complete-now', [QueueController::class, 'completeNow']);
        Route::post('/queue/cancel-now', [QueueController::class, 'cancelNow']);
        Route::post('/queue/requeue-now', [QueueController::class, 'requeueNow']);

        
        Route::get('/admin/queue', [QueueController::class, 'index'])->name('admin.queue.index');
    });

    // ✅ Test route for QR generation 
    Route::get('/test-qr', function () {
        $qr = QrCode::format('png')->size(200)->generate('Testing QR output');
        Storage::disk('public')->put('qrcodes/test_qr.png', $qr);
        return 'QR Test saved to storage/app/public/qrcodes/test_qr.png';
    });