<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

class BillingController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);

        $user = $request->user();
        $schoolId = $user->school_id ?? null;

        $stripe = new StripeClient(config('services.stripe.secret'));

        // Ensure customer exists
        $subscription = Subscription::where('school_id', $schoolId)->first();

        $customerId = $subscription->stripe_customer_id ?? null;

        if (! $customerId) {
            $customer = $stripe->customers->create([
                'email' => $user->email,
                'metadata' => ['school_id' => $schoolId],
            ]);
            $customerId = $customer->id;

            if ($subscription) {
                $subscription->update(['stripe_customer_id' => $customerId]);
            } else {
                Subscription::create([
                    'school_id' => $schoolId,
                    'stripe_customer_id' => $customerId,
                    'status' => 'pending',
                ]);
            }
        }

        // Create a Checkout Session for a subscription
        $session = $stripe->checkout->sessions->create([
            'customer' => $customerId,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $request->input('price_id'),
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url') . '/billing/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.frontend_url') . '/billing/cancel',
            'metadata' => [
                'school_id' => $schoolId,
            ],
        ]);

        return response()->json(['url' => $session->url, 'id' => $session->id]);
    }

    public function subscription(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id ?? null;

        $subscription = Subscription::where('school_id', $schoolId)->latest()->first();

        if (! $subscription) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $subscription]);
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id ?? null;

        $subscription = Subscription::where('school_id', $schoolId)->whereNotNull('stripe_subscription_id')->latest()->first();

        if (! $subscription) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $stripe->subscriptions->update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $subscription->update(['status' => 'canceled']);

            return response()->json(['message' => 'Subscription will be canceled at period end']);
        } catch (\Exception $e) {
            Log::error('Stripe cancel error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to cancel subscription'], 500);
        }
    }

    // Public webhook endpoint
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        $type = $event->type;

        switch ($type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                // Attach subscription record when checkout completes
                $schoolId = $session->metadata->school_id ?? null;

                if ($session->subscription) {
                    Subscription::updateOrCreate(
                        ['school_id' => $schoolId],
                        [
                            'stripe_subscription_id' => $session->subscription,
                            'stripe_customer_id' => $session->customer ?? null,
                            'status' => 'active',
                        ]
                    );
                }
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $stripeSubscriptionId = $invoice->subscription ?? null;
                if ($stripeSubscriptionId) {
                    $sub = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
                    if ($sub) {
                        $sub->update(['status' => 'active']);
                    }
                }
                break;

            case 'customer.subscription.deleted':
                $stripeSub = $event->data->object;
                $stripeSubscriptionId = $stripeSub->id ?? null;
                if ($stripeSubscriptionId) {
                    $sub = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
                    if ($sub) {
                        $sub->update(['status' => 'canceled']);
                    }
                }
                break;

            default:
                // Log unhandled events for later inspection
                Log::info('Unhandled Stripe webhook event: ' . $type);
                break;
        }

        return response('Webhook handled', 200);
    }
}
