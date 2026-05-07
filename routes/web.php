<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use Laravel\Fortify\Features;

Route::livewire('/', 'pages::home-page')->name('home');

Route::livewire('products', 'pages::products-list')->name('products.index');
Route::livewire('product/{slug}', 'pages::product-details')->name('products.show');
Route::livewire('cart', 'pages::cart-page')->name('cart.index');

// Customer route
Route::middleware('auth:customer')->group(function () {
    Route::livewire('checkout', 'pages::checkout-page')->name('checkout');
    Route::livewire('account', 'pages::customer.dashboard')->name('customer.dashboard');

    Route::livewire('account/orders', 'pages::customer.orders')->name('customer.orders');
    Route::livewire('account/orders/{id}', 'pages::customer.order-details')->name('customer.orders.show');
    Route::livewire('account/profile', 'pages::customer.profile')->name('customer.profile');

    Route::get('/checkout/success/{order}', [CheckoutController::class,'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{order}', [CheckoutController::class,'cancel'])->name('checkout.cancel');

    Route::post('/logout', function () {
        auth('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
    Route::livewire('settings/password', 'pages::settings.security')->name('user-password.edit');
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/two-factor', 'pages::settings.two-factor-setup-modal')->name('two-factor.show');

    // Route::get('settings/two-factor', TwoFactor::class)
    //     ->middleware(
    //         when(
    //             Features::canManageTwoFactorAuthentication()
    //                 && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
    //             ['password.confirm'],
    //             [],
    //         ),
    //     )->name('two-factor.show');
});

require __DIR__ . '/settings.php';
