<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionReceipt;
use App\Models\Invoice;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {

            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

        } catch (\UnexpectedValueException $e) {

            // Invalid payload
            Log::error('Stripe Webhook Invalid Payload');

            return response()->json([
                'message' => 'Invalid payload'
            ], 400);

        } catch (\Stripe\Exception\SignatureVerificationException $e) {

            // Invalid signature
            Log::error('Stripe Webhook Invalid Signature');

            return response()->json([
                'message' => 'Invalid signature'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Handle Stripe Events
        |--------------------------------------------------------------------------
        */

        switch ($event->type) {

            case 'checkout.session.completed':

                $session = $event->data->object;

                Log::info('Stripe checkout session completed', [
                    'session_id' => $session->id
                ]);

                $schoolId = $session->metadata->school_id ?? null;
                $planType = $session->metadata->plan_type ?? null;

                if ($schoolId && $planType) {

                    $school = School::find($schoolId);

                    if ($school) {

                        $school->update([
                            'plan_type' => $planType,
                            'subscription_status' => 'active',
                            'subscription_expires_at' => Carbon::now()->addMonth(),
                        ]);

                        // Create invoice
                        $invoice = Invoice::create([
                            'school_id' => $school->id,
                            'amount' => 100, // example, should get from stripe
                            'paid_amount' => 100,
                            'balance' => 0,
                            'status' => 'paid',
                        ]);

                        // Send receipt email
                        Mail::to($school->users->first()->email)->send(new SubscriptionReceipt($invoice));

                        Log::info('School subscription activated', [
                            'school_id' => $schoolId,
                            'plan' => $planType
                        ]);
                    }
                }

            break;

            case 'invoice.payment_succeeded':

                Log::info('Stripe invoice payment succeeded');

            break;

            case 'customer.subscription.deleted':

                Log::info('Stripe subscription cancelled');

            break;

        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}