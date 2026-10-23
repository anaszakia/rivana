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
use App\Http\Controllers\LanguageController;

// Welcome route - replace login
Route::get('/welcome', [WelcomeController::class, 'welcome'])
     ->name('welcome');

// Language switcher route
Route::post('/language/switch', [LanguageController::class, 'switch'])
     ->name('language.switch');

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
    
    // Audit Log routes
    Route::get('/audit', [AuditLogController::class, 'index'])
        ->name('audit.index');
    Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])
        ->name('audit.show');
    Route::post('/audit/export', [AuditLogController::class, 'export'])
        ->name('audit.export');


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

        //das info
        Route::post('/das-info', [HidrologiJobController::class, 'dasInfo'])
            ->name('das-info');
        
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