<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Super Admin Bypass
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Ensure User Has School
        |--------------------------------------------------------------------------
        */
        if (!$user->school) {
            return response()->json([
                'message' => 'School not found for this user.'
            ], 404);
        }

        $school = $user->school;

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Check Subscription Status
        |--------------------------------------------------------------------------
        */
        if ($school->subscription_status === 'cancelled') {
            return response()->json([
                'message' => 'Subscription cancelled. Please renew to continue.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Check Expiration
        |--------------------------------------------------------------------------
        */
        if (
            $school->subscription_expires_at &&
            Carbon::now()->greaterThan($school->subscription_expires_at)
        ) {
            return response()->json([
                'message' => 'Subscription expired. Please upgrade or renew.'
            ], 403);
        }

        return $next($request);
    }
}