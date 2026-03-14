<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\School;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function subscription(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $subscription = Subscription::where('school_id', $school->id)->first();

        return response()->json(['subscription' => $subscription]);
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $subscription = Subscription::where('school_id', $school->id)->first();

        if (!$subscription || !$subscription->stripe_subscription_id) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $stripe->subscriptions->cancel($subscription->stripe_subscription_id);

            $subscription->update(['status' => 'cancelled']);
            $school->update(['subscription_status' => 'cancelled']);

            return response()->json(['message' => 'Subscription cancelled']);

        } catch (\Throwable $e) {
            Log::error('Stripe cancel error: '.$e->getMessage());
            return response()->json(['message' => 'Failed to cancel subscription'], 500);
        }
    }
}
