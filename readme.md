## Laravel Bank

This is a package to connect to any Internet Payment Gateways.</br>
This package now only supports `Mellat` IPG for now but we are developing other IPGs ASAP.

## Installation

Require this package with composer.

```shell
composer require farsh4d/laravel-bank
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

### Laravel without auto-discovery:

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```php
Farsh4d\Bank\Providers\BankServiceProvider::class,
```

#### Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="Farsh4d\Bank\Providers\BankServiceProvider"
```

#### Prepare your database

```shell
php artisan migrate
```

## Usage

You need 2 routes, one to start a payment and one for callback:

*routes.php*
```php
Route::get('/payment-start', 'PaymentController@start')->name('payment_start');
Route::any('/payment-callback', 'PaymentController@callback')->name('payment_callback');
```

>Remember to exclude your callback route from CSRF verification.

In `start` method initialize your desired driver and set required data (price, orderId and callback):

*PaymentController.php @start*
```php
$amount = 50000;
$orderId = '123456';

$bankDriver = Bank::driver('Mellat');

$bankDriver->price($amount)
    ->orderId($orderId)
    ->callback(route('payment_callback'));

try {
    $bankDriver->ready();
    return $bankDriver->redirect();
} catch (\Exception $e) {
    // Handle failed transaction initiation; ie:
    return view('payment-error');
}
```

If everything goes right user redirects to IPG and makes a payment and then redirects to your callback.

*PaymentController.php @verify*
```php
try {
    Bank::verify();
    // Show transaction success view or do sth else; ie:
    return view('payment-success');
} catch (\Exception $e) {
    // Handle failed payment; ie:
    return view('payment-failed');
}
```