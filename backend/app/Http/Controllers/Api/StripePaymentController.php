<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\School;

class StripePaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Create Subscription Checkout Session
    |--------------------------------------------------------------------------
    */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:basic,pro,enterprise',
        ]);

        $user = $request->user();
        $school = $user->school;

        Stripe::setApiKey(config('services.stripe.secret'));

        // Map plan_type to Stripe Price ID
        $priceId = match ($request->plan_type) {
            'basic' => env('STRIPE_BASIC_PRICE_ID'),
            'pro' => env('STRIPE_PRO_PRICE_ID'),
            'enterprise' => env('STRIPE_ENTERPRISE_PRICE_ID'),
        };

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => url('/payment-success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/payment-cancelled'),
            'metadata' => [
                'school_id' => $school->id,
                'plan_type' => $request->plan_type,
            ],
        ]);

        return response()->json([
            'checkout_url' => $session->url
        ]);
    }
}