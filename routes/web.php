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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes (PRIORITY - Harus di paling atas)
|--------------------------------------------------------------------------
*/

// Webhook route tanpa middleware apapun
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])
    ->withoutMiddleware(['web', 'auth', 'verified'])
    ->name('midtrans.webhook');

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

// Status pesanan
Route::get('/statuspesanan', function () {
    return view('statuspesanan');
})->name('statuspesanan');

// Kontak form
Route::post('/kontak/kirim', [KontakController::class, 'kirim'])->name('kontak.kirim');

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Perlu login)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
    Route::get('/orders/{order}/sync-payment', [OrderController::class, 'syncPaymentStatus'])->name('orders.syncPayment');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';