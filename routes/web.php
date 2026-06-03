<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\User\SummaryController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\ChatbotController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AvailabilityController as AdminAvailabilityController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\ReservationManagementController;
use App\Http\Controllers\Admin\CampusManagementController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Not logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    // Password Reset
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::get('/availability', [AvailabilityController::class, 'index'])->name('public.availability.index');
Route::get('/availability/events', [AvailabilityController::class, 'getEvents'])->name('public.availability.events');
Route::get('/availability/day', [AvailabilityController::class, 'getDayEvents'])->name('public.availability.day');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (Logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // ========== USER ROUTES ==========
    Route::middleware(['checkUserRole'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/user/dashboard/events', [UserDashboardController::class, 'getEvents'])->name('user.dashboard.events');
        Route::get('/user/dashboard/upcoming', [UserDashboardController::class, 'getUpcomingEvents'])->name('user.dashboard.upcoming');
        Route::get('/user/dashboard/day', [UserDashboardController::class, 'getDayEvents'])->name('user.dashboard.day');
        
        // Reservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('user.reservations');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('user.reservations.store');
        Route::get('/reservations/availability/{id}', [ReservationController::class, 'showAvailability'])->name('user.reservations.availability');
        Route::get('/api/campuses/{campusId}/establishments', [ReservationController::class, 'getEstablishmentsByCampus']);
        
        // Summary
        Route::get('/summary', [SummaryController::class, 'index'])->name('user.summary');
        Route::get('/user/reservations/{id}/details', [SummaryController::class, 'getDetails'])->name('user.reservations.details');
        
        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('user.settings');
        Route::post('/settings/password', [SettingsController::class, 'changePassword'])->name('user.settings.password');
        
        // Chatbot
        Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/chatbot/process', [ChatbotController::class, 'processMessage'])->name('chatbot.process');
        Route::post('/chatbot/cancel', [ChatbotController::class, 'cancelReservation'])->name('chatbot.cancel');
        
        // Chat (User to Admin)
        Route::get('/chat', [App\Http\Controllers\User\ChatController::class, 'index'])->name('user.chat');
        Route::get('/chat/history', [ChatbotController::class, 'getChatHistory'])->name('chat.history');
        Route::post('/user/chat/send', [App\Http\Controllers\User\ChatController::class, 'sendMessage'])->name('user.chat.send');
        Route::get('/user/chat/messages', [App\Http\Controllers\User\ChatController::class, 'getMessages'])->name('user.chat.messages');
        Route::get('/user/chat/unread-count', [App\Http\Controllers\User\ChatController::class, 'getUnreadCount'])->name('user.chat.unread');
    });
    
    // Temporary Password Change (Forced)
    Route::get('/change-password', function () {
        if (!auth()->user()->is_password_generated) {
            return redirect()->route('dashboard');
        }
        return view('auth.change-password');
    })->name('password.change');
    
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');
    
    // Reports
    Route::get('/report/single/{id}', [ReportController::class, 'generateSingleReport'])->name('report.single');
    Route::get('/report/all', [ReportController::class, 'generateAllReport'])->name('report.all');
    
    // User Status Pages
    Route::get('/user/pending', function () {
        if (!auth()->user()->isPending()) {
            return redirect()->route('dashboard');
        }
        return view('user.pending');
    })->name('user.pending');
    
    Route::get('/user/rejected', function () {
        if (!auth()->user()->isRejected()) {
            return redirect()->route('dashboard');
        }
        return view('user.rejected');
    })->name('user.rejected');
    
    // ========== ADMIN ROUTES ==========
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Availability Calendar
        Route::get('/availability', [AdminAvailabilityController::class, 'index'])->name('availability.index');
        Route::get('/availability/events', [AdminAvailabilityController::class, 'getEvents'])->name('availability.events');
        Route::get('/availability/day', [AdminAvailabilityController::class, 'getDayEvents'])->name('availability.day');
        Route::get('/availability/upcoming', [AdminAvailabilityController::class, 'getUpcomingEvents'])->name('availability.upcoming');
        
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
        Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/bulk-approve', [UserManagementController::class, 'bulkApprove'])->name('users.bulk-approve');
        
        // Reservation Management
        Route::get('/reservations', [ReservationManagementController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{id}', [ReservationManagementController::class, 'show'])->name('reservations.show');
        Route::get('/reservations/{id}/edit', [ReservationManagementController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{id}', [ReservationManagementController::class, 'update'])->name('reservations.update');
        Route::post('/reservations/{id}/approve', [ReservationManagementController::class, 'approve'])->name('reservations.approve');
        Route::post('/reservations/{id}/reject', [ReservationManagementController::class, 'reject'])->name('reservations.reject');
        Route::delete('/reservations/{id}', [ReservationManagementController::class, 'destroy'])->name('reservations.destroy');
        
        // Campus Management
        Route::get('/campuses', [CampusManagementController::class, 'index'])->name('campuses.index');
        Route::post('/campuses', [CampusManagementController::class, 'store'])->name('campuses.store');
        Route::put('/campuses/{id}', [CampusManagementController::class, 'update'])->name('campuses.update');
        Route::post('/campuses/{id}/toggle-status', [CampusManagementController::class, 'toggleStatus'])->name('campuses.toggle-status');
        Route::delete('/campuses/{id}', [CampusManagementController::class, 'destroy'])->name('campuses.destroy');
        
        // Establishment Management
        Route::get('/campuses/{campusId}/establishments', [CampusManagementController::class, 'getEstablishments'])->name('campuses.establishments');
        Route::get('/campuses/{campusId}/establishments/{id}', [CampusManagementController::class, 'getEstablishment'])->name('campuses.establishments.show');
        Route::post('/campuses/{campusId}/establishments', [CampusManagementController::class, 'storeEstablishment'])->name('campuses.establishments.store');
        Route::put('/campuses/{campusId}/establishments/{id}', [CampusManagementController::class, 'updateEstablishment'])->name('campuses.establishments.update');
        Route::post('/campuses/{campusId}/establishments/{id}/toggle-status', [CampusManagementController::class, 'toggleEstablishmentStatus'])->name('campuses.establishments.toggle-status');
        Route::delete('/campuses/{campusId}/establishments/{id}', [CampusManagementController::class, 'destroyEstablishment'])->name('campuses.establishments.destroy');
        
        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/password', [AdminSettingsController::class, 'changePassword'])->name('settings.password');
        Route::get('/settings/backup', [AdminSettingsController::class, 'backup'])->name('settings.backup');
        Route::post('/settings/restore', [AdminSettingsController::class, 'restore'])->name('settings.restore');
        Route::get('/settings/backups/list', [AdminSettingsController::class, 'getBackupList'])->name('settings.backups.list');
        Route::get('/settings/backup/download/{filename}', [AdminSettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::delete('/settings/backup/delete/{filename}', [AdminSettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all');
        Route::get('/notifications/{id}/redirect', [NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.redirect');
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread');
        Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
        
        // Admin Chat
        Route::get('/chat', [AdminChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/send', [AdminChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/chat/unread-count', [AdminChatController::class, 'getUnreadCount'])->name('chat.unread');

        // Admin Chat End Session
        Route::post('/chat/end-session', [App\Http\Controllers\Admin\AdminChatController::class, 'endSession'])->name('chat.end');
        
        // Admin Management (Super Admin only)
        Route::middleware(['super_admin'])->group(function () {
            Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
            Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
            Route::delete('/admins/{id}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| HOME ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin() 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});