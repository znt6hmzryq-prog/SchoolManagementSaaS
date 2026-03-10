<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Student;

class CheckStudentLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->school) {
            return response()->json([
                'message' => 'School not found.'
            ], 403);
        }

        $school = $user->school;

        $studentCount = Student::where('school_id', $school->id)->count();

        if ($school->max_students !== null && $studentCount >= $school->max_students) {
            return response()->json([
                'message' => 'Student limit reached. Please upgrade your plan.'
            ], 403);
        }

        return $next($request);
    }
}