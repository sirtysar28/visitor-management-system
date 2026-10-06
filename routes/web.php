<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public : Landing Page (PWA)
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/offline', fn () => view('offline'))->name('offline');

/*
|--------------------------------------------------------------------------
| Guest : Login + Captcha
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/captcha', CaptchaController::class)->name('captcha');
});

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Visitors (registrasi tamu: pre-registered, walk-in, vendor, delivery)
    Route::resource('visitors', VisitorController::class);
    Route::post('/visitors/{visitor}/check-in', [VisitorController::class, 'checkIn'])->name('visitors.checkin');
    Route::post('/visitors/{visitor}/check-out', [VisitorController::class, 'checkOut'])->name('visitors.checkout');

    // Approvals
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{visitor}/decide', [ApprovalController::class, 'decide'])->name('approvals.decide');

    // Blacklist / Watchlist
    Route::get('/blacklist', [BlacklistController::class, 'index'])->name('blacklist.index');
    Route::post('/blacklist', [BlacklistController::class, 'store'])->name('blacklist.store');
    Route::post('/blacklist/{blacklist}/toggle', [BlacklistController::class, 'toggle'])->name('blacklist.toggle');
    Route::delete('/blacklist/{blacklist}', [BlacklistController::class, 'destroy'])->name('blacklist.destroy');

    // Reports (+ export PDF/Excel)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');

    // Notifications (bell pojok kanan atas)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');

    // Profile (ganti password + toggle intip password)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Audit trail
    Route::get('/audit', [ProfileController::class, 'auditIndex'])->name('audit.index');
    Route::get('/audit/export', [ProfileController::class, 'exportCsv'])->name('audit.export');

    /*
    | Admin only
    */
    Route::middleware('can:admin-only')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
        Route::post('/settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.smtp');
        Route::post('/settings/smtp/test', [SettingController::class, 'testSmtp'])->name('settings.smtp.test');

        Route::resource('users', UserController::class);
    });
});
