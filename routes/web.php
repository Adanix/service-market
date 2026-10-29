<?php

use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminPayoutController;
use App\Http\Controllers\AdminProviderController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProviderAccountController;
use App\Http\Controllers\ProviderBookingController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\ProviderProfileController;
use App\Http\Controllers\ProviderServiceController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/docs', [PageController::class, 'docs'])->name('docs');
Route::get('/help', [PageController::class, 'help'])->name('help');
Route::post('/contact', [PageController::class, 'contact'])->name('contact.submit')->middleware('throttle:5,1');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/refunds', [PageController::class, 'refunds'])->name('refunds');

// Services and Providers (Public)
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/providers/{provider:slug}', [ProviderProfileController::class, 'show'])->name('providers.show');

// Demo authentication
Route::get('/demo/sign-in', [DemoAuthController::class, 'show'])->name('demo.sign-in');
Route::post('/demo/sign-in/{user}', [DemoAuthController::class, 'signIn'])->name('demo.sign-in.as');
Route::post('/demo/login/{user}', [DemoAuthController::class, 'signIn'])->name('demo.login');
Route::match(['get', 'post'], '/demo/sign-out', [DemoAuthController::class, 'signOut'])->name('demo.sign-out');

// Customer area
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking:reference}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking:reference}/confirm', [CustomerBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking:reference}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking:reference}/receipt', [CustomerBookingController::class, 'receipt'])->name('bookings.receipt');
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
});

// Checkout (Customer only)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/checkout/{service:slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{service:slug}', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('throttle:10,1');
});

// Provider area
Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    Route::get('/services', [ProviderServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ProviderServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ProviderServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ProviderServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ProviderServiceController::class, 'update'])->name('services.update');
    Route::post('/services/{service}/submit', [ProviderServiceController::class, 'submit'])->name('services.submit');
    Route::post('/services/{service}/archive', [ProviderServiceController::class, 'archive'])->name('services.archive');

    Route::get('/bookings', [ProviderBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking:reference}', [ProviderBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking:reference}/accept', [ProviderBookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking:reference}/decline', [ProviderBookingController::class, 'decline'])->name('bookings.decline');
    Route::post('/bookings/{booking:reference}/start', [ProviderBookingController::class, 'start'])->name('bookings.start');
    Route::post('/bookings/{booking:reference}/complete', [ProviderBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking:reference}/cancel', [ProviderBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/profile', [ProviderAccountController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProviderAccountController::class, 'update'])->name('profile.update');
    Route::get('/earnings', [ProviderAccountController::class, 'earnings'])->name('earnings');
});

// Notifications (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Administrator area
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');

    Route::get('/providers', [AdminProviderController::class, 'index'])->name('providers.index');
    Route::get('/providers/{provider}', [AdminProviderController::class, 'show'])->name('providers.show');
    Route::post('/providers/{provider}/approve', [AdminProviderController::class, 'approve'])->name('providers.approve');
    Route::post('/providers/{provider}/suspend', [AdminProviderController::class, 'suspend'])->name('providers.suspend');

    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::post('/services/{service}/approve', [AdminServiceController::class, 'approve'])->name('services.approve');
    Route::post('/services/{service}/reject', [AdminServiceController::class, 'reject'])->name('services.reject');
    Route::post('/services/{service}/archive', [AdminServiceController::class, 'archive'])->name('services.archive');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking:reference}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking:reference}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking:reference}/notes', [AdminBookingController::class, 'storeNote'])->name('bookings.notes');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/capture', [AdminPaymentController::class, 'capture'])->name('payments.capture');
    Route::post('/payments/{payment}/void', [AdminPaymentController::class, 'void'])->name('payments.void');
    Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');

    Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [AdminPayoutController::class, 'store'])->name('payouts.store');
    Route::post('/payouts/{payout}/mark-paid', [AdminPayoutController::class, 'markPaid'])->name('payouts.mark-paid');

    Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    Route::get('/audit', [AdminAuditController::class, 'index'])->name('audit.index');

    Route::get('/contact', [AdminContactController::class, 'index'])->name('contact.index');
    Route::post('/contact/{message}/read', [AdminContactController::class, 'markRead'])->name('contact.read');
});
