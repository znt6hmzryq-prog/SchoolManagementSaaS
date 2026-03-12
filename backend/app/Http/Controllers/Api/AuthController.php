<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Services\SchoolSetupService;
use App\Services\AuditLogger;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register School (Automatic Setup)
    |--------------------------------------------------------------------------
    */
    public function registerSchool(Request $request, SchoolSetupService $schoolSetup)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'country' => 'sometimes|string|max:100',
            'plan' => 'sometimes|string|in:basic,pro,enterprise',
        ]);

        $school = $schoolSetup->setup($request->all());

        // Get the created admin user
        $admin = $school->users->first();

        // Log audit
        AuditLogger::log($admin, 'school_created', 'school', $school->id);

        // Create Sanctum token for the admin
        $token = $admin->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $admin,
            'role' => $admin->role,
            'school_id' => $school->id,
            'message' => 'School registered successfully'
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Login (Rate Limited)
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Create unique rate limit key
        $key = Str::lower($request->email) . '|' . $request->ip();

        // Allow only 5 attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {

            // Count failed attempt
            RateLimiter::hit($key, 60);

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Clear attempts after successful login
        RateLimiter::clear($key);

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        AuditLogger::log($user, 'user_logged_in', 'user', $user->id);

        return response()->json([
            'token' => $token,
            'user' => $user,
            'role' => $user->role,
            'school_id' => $user->school_id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        AuditLogger::log($request->user(), 'user_logged_out', 'user', $request->user()->id);

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}