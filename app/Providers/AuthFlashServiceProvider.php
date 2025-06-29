<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Logout;

class AuthFlashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Flash message after successful login
        Event::listen(Login::class, function ($event) {
            session()->flash('success', 'Login berhasil!');
        });

        // Flash message after successful registration
        Event::listen(Registered::class, function ($event) {
            session()->flash('success', 'Registrasi berhasil! Silakan login untuk melanjutkan.');
        });

        // Optional: Flash message after logout
        Event::listen(Logout::class, function ($event) {
            session()->flash('success', 'Anda telah berhasil logout.');
        });
    }
}