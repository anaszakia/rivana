<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HidrologiJobController;
use App\Http\Controllers\HidrologiFileController;


// hanya bisa diakses tamu (belum login)
Route::middleware('guest')->group(function () {
    // Form login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('login');

    // Proses login
    Route::post('/login', [LoginController::class, 'login'])
         ->middleware('log.sensitive')
         ->name('login.submit');

    // Form register
    Route::get('/register', [LoginController::class, 'showRegisterForm'])
         ->name('register');

    // Proses register
    Route::post('/register', [LoginController::class, 'register'])
         ->middleware('log.sensitive')
         ->name('register.submit');
});

// Logout (method POST demi keamanan; pakai @csrf di form logout)
Route::post('/logout', [LoginController::class, 'logout'])
     ->middleware(['auth', 'log.sensitive'])
     ->name('logout');

// Test route for debugging form submission (REMOVE IN PRODUCTION!)
Route::get('/test-submit', function () {
    return view('test-submit');
})->middleware('auth')->name('test.submit');

// Test route for API preview (REMOVE IN PRODUCTION!)
Route::get('/test-api-preview', function () {
    return view('test-api-preview');
})->middleware('auth')->name('test.api.preview');


// Authenticated routes - Permission based access control
Route::middleware(['auth', 'log.sensitive'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:edit profile')
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:edit profile')
        ->name('profile.update');
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');
    
    // User management routes
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:view users')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:create users')
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:create users')
        ->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('permission:view users')
        ->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:edit users')
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:edit users')
        ->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:delete users')
        ->name('users.destroy');
    
    // Audit Log routes
    Route::get('/audit', [AuditLogController::class, 'index'])
        ->middleware('permission:view audit logs')
        ->name('audit.index');
    Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])
        ->middleware('permission:view audit logs')
        ->name('audit.show');
    Route::post('/audit/export', [AuditLogController::class, 'export'])
        ->middleware('permission:export audit logs')
        ->name('audit.export');
    
    // Role management routes
    Route::resource('roles', App\Http\Controllers\RoleController::class)
        ->middleware('permission:view roles');
    Route::get('/roles/{role}', [App\Http\Controllers\RoleController::class, 'show'])
        ->middleware('permission:view roles')
        ->name('roles.show');
    Route::get('/roles/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])
        ->middleware('permission:edit roles')
        ->name('roles.edit');
    Route::put('/roles/{role}', [App\Http\Controllers\RoleController::class, 'update'])
        ->middleware('permission:edit roles')
        ->name('roles.update');
    Route::delete('/roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])
        ->middleware('permission:delete roles')
        ->name('roles.destroy');
    
    // Permission management routes
    Route::resource('permissions', App\Http\Controllers\PermissionController::class)
        ->middleware('permission:view permissions');
    Route::get('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'show'])
        ->middleware('permission:view permissions')
        ->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [App\Http\Controllers\PermissionController::class, 'edit'])
        ->middleware('permission:edit permissions')
        ->name('permissions.edit');
    Route::put('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'update'])
        ->middleware('permission:edit permissions')
        ->name('permissions.update');
    Route::delete('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'destroy'])
        ->middleware('permission:delete permissions')
        ->name('permissions.destroy');

    // Hidrologi job routes
    Route::prefix('hidrologi')->name('hidrologi.')->group(function () {
        // Halaman utama - daftar job
        Route::get('/', [HidrologiJobController::class, 'index'])
            ->middleware('permission:view hidrologi')
            ->name('index');
        
        // Form submit job baru
        Route::get('/create', [HidrologiJobController::class, 'create'])
            ->middleware('permission:create hidrologi')
            ->name('create');
        
        // Submit job ke API
        Route::post('/submit', [HidrologiJobController::class, 'store'])
            ->middleware('permission:create hidrologi')
            ->name('submit');
        
        // Cek status job (AJAX)
        Route::get('/status/{id}', [HidrologiJobController::class, 'checkStatus'])
            ->middleware('permission:view hidrologi')
            ->name('status');
        
        // Lihat hasil job
        Route::get('/show/{id}', [HidrologiJobController::class, 'show'])
            ->middleware('permission:view hidrologi')
            ->name('show');
        
        // Lihat summary (structured)
        Route::get('/summary/{id}', [HidrologiJobController::class, 'getSummary'])
            ->middleware('permission:view hidrologi')
            ->name('summary');
        
        // Lihat full logs lengkap (all output)
        Route::get('/logs/{id}', [HidrologiJobController::class, 'getLogs'])
            ->middleware('permission:view hidrologi')
            ->name('logs');
        
        // Cancel job
        Route::post('/cancel/{id}', [HidrologiJobController::class, 'cancel'])
            ->middleware('permission:edit hidrologi')
            ->name('cancel');
        
        // Delete job
        Route::delete('/delete/{id}', [HidrologiJobController::class, 'destroy'])
            ->middleware('permission:delete hidrologi')
            ->name('destroy');
        
        // File routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::get('/download/{id}', [HidrologiFileController::class, 'download'])
                ->middleware('permission:download hidrologi files')
                ->name('download');
            Route::get('/preview/{id}', [HidrologiFileController::class, 'preview'])
                ->middleware('permission:view hidrologi')
                ->name('preview');
            Route::get('/info/{id}', [HidrologiFileController::class, 'info'])
                ->middleware('permission:view hidrologi')
                ->name('info');
        });
    });
});

Route::redirect('/', '/login');