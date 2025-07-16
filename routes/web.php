<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Public Routes (Tidak perlu login)
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', function () {
    return view('index');
})->name('home');

// Halaman statis
Route::get('/cerita', function () {
    return view('cerita');
})->name('cerita');

Route::get('/proses', function () {
    return view('proses');
})->name('proses');

Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

// Halaman produk
Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.detail');

// Checkout routes
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::post('/checkout', [CheckoutController::class, 'processPayment'])->name('checkout.process');

// Kontak form
Route::post('/kontak/kirim', [KontakController::class, 'kirim'])->name('kontak.kirim');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Perlu login)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Order routes - PERBAIKAN: Typo di parameter
    Route::get('/my-orders', [UserOrderController::class, 'index'])->name('user.orders.index');
    Route::get('/my-orders/{order}', [UserOrderController::class, 'show'])->name('user.orders.show');
    Route::patch('/my-orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('user.orders.cancel');
    Route::patch('/my-orders/{order}/confirm-received', [UserOrderController::class, 'confirmReceived'])->name('user.orders.confirm-received');
    
    // Payment routes
    Route::get('/payment/{orderId}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{orderId}/process', [PaymentController::class, 'process'])->name('payment.process');
    
    // Products route (untuk user yang login)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // web.php
    Route::post('/my-orders/{order}/pay', [UserOrderController::class, 'payOrder'])->name('user.orders.pay');

});

/*
|--------------------------------------------------------------------------
| Admin Routes (Perlu login sebagai admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);

    // Routes yang sudah ada
    Route::get('/orders/{order}/sync-payment', [OrderController::class, 'syncPaymentStatus'])->name('orders.syncPayment');

    // Routes untuk update status manual
    Route::post('/orders/{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])
        ->name('orders.updatePaymentStatus');
    Route::post('/orders/{order}/update-order-status', [OrderController::class, 'updateOrderStatus'])
        ->name('orders.updateOrderStatus');
    //Status order
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatusAjax'])
        ->name('orders.updateStatus');
});