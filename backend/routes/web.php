<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are for Stripe redirect pages (NOT API).
| Stripe will redirect users here after payment.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Stripe Billing Redirect Pages
|--------------------------------------------------------------------------
*/

Route::get('/billing/success', function () {
    return response()->json([
        'message' => 'Payment successful. Subscription activated.',
        'status' => 'success'
    ]);
});

Route::get('/billing/cancel', function () {
    return response()->json([
        'message' => 'Payment was cancelled.',
        'status' => 'cancelled'
    ]);
});