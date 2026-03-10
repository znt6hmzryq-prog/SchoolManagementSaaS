<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSchoolScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Super admin can access everything
        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        // Users must belong to a school
        if (!$user || !$user->school_id) {
            return response()->json([
                'message' => 'User not assigned to any school'
            ], 403);
        }

        return $next($request);
    }
}