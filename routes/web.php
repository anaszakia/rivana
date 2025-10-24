<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HidrologiJobController;
use App\Http\Controllers\HidrologiFileController;
use App\Http\Controllers\WelcomeController;


// Welcome route - replace login
Route::get('/welcome', [WelcomeController::class, 'welcome'])
     ->name('welcome');

// Public routes - No authentication needed
Route::group([], function () {
    // Profile routes
    // Route::get('/profile', [ProfileController::class, 'edit'])
    //     ->name('profile.edit');
    // Route::put('/profile', [ProfileController::class, 'update'])
    //     ->name('profile.update');
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');
    
    // User management routes
    // Route::get('/users', [UserController::class, 'index'])
    //     ->name('users.index');
    // Route::get('/users/create', [UserController::class, 'create'])
    //     ->name('users.create');
    // Route::post('/users', [UserController::class, 'store'])
    //     ->name('users.store');
    // Route::get('/users/{user}', [UserController::class, 'show'])
    //     ->name('users.show');
    // Route::get('/users/{user}/edit', [UserController::class, 'edit'])
    //     ->name('users.edit');
    // Route::put('/users/{user}', [UserController::class, 'update'])
    //     ->name('users.update');
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])
    //     ->name('users.destroy');
    
    // Audit Log routes
    Route::get('/audit', [AuditLogController::class, 'index'])
        ->name('audit.index');
    Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])
        ->name('audit.show');
    Route::post('/audit/export', [AuditLogController::class, 'export'])
        ->name('audit.export');
    
    // // Role management routes
    // Route::resource('roles', App\Http\Controllers\RoleController::class);
    // Route::get('/roles/{role}', [App\Http\Controllers\RoleController::class, 'show'])
    //     ->name('roles.show');
    // Route::get('/roles/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])
    //     ->name('roles.edit');
    // Route::put('/roles/{role}', [App\Http\Controllers\RoleController::class, 'update'])
    //     ->name('roles.update');
    // Route::delete('/roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])
    //     ->name('roles.destroy');
    
    // // Permission management routes
    // Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    // Route::get('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'show'])
    //     ->name('permissions.show');
    // Route::get('/permissions/{permission}/edit', [App\Http\Controllers\PermissionController::class, 'edit'])
    //     ->name('permissions.edit');
    // Route::put('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'update'])
    //     ->name('permissions.update');
    // Route::delete('/permissions/{permission}', [App\Http\Controllers\PermissionController::class, 'destroy'])
    //     ->name('permissions.destroy');

    // Hidrologi job routes
    Route::prefix('hidrologi')->name('hidrologi.')->group(function () {
        // Halaman utama - daftar job
        Route::get('/', [HidrologiJobController::class, 'index'])
            ->name('index');
        
        // Form submit job baru
        Route::get('/create', [HidrologiJobController::class, 'create'])
            ->name('create');
        
        // Submit job ke API
        Route::post('/submit', [HidrologiJobController::class, 'store'])
            ->name('submit');
        
        // Cek status job (AJAX)
        Route::get('/status/{id}', [HidrologiJobController::class, 'checkStatus'])
            ->name('status');
        
        // Lihat hasil job
        Route::get('/show/{id}', [HidrologiJobController::class, 'show'])
            ->name('show');
        
        // Lihat summary (structured)
        Route::get('/summary/{id}', [HidrologiJobController::class, 'getSummary'])
            ->name('summary');
        
        // Lihat full logs lengkap (all output)
        Route::get('/logs/{id}', [HidrologiJobController::class, 'getLogs'])
            ->name('logs');
        
        // Cancel job
        Route::post('/cancel/{id}', [HidrologiJobController::class, 'cancel'])
            ->name('cancel');
        
        // Delete job
        Route::delete('/delete/{id}', [HidrologiJobController::class, 'destroy'])
            ->name('destroy');
        
        // Bulk delete jobs
        Route::post('/bulk-delete', [HidrologiJobController::class, 'bulkDestroy'])
            ->name('bulk-destroy');
        
        // File routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::get('/download/{id}', [HidrologiFileController::class, 'download'])
                ->name('download');
            Route::get('/preview/{id}', [HidrologiFileController::class, 'preview'])
                ->name('preview');
            Route::get('/info/{id}', [HidrologiFileController::class, 'info'])
                ->name('info');
        });
    });
});

Route::redirect('/', '/welcome');