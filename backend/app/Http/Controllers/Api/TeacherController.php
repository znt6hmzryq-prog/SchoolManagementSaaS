<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class TeacherController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List Teachers (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $teachers = Teacher::where('school_id', $schoolId)->get();

        return response()->json($teachers);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Teacher (Plan Enforced)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'nullable|email',
            'phone'      => 'nullable|string|max:20',
        ]);

        $school = $request->user()->school;

        // 🔥 Enforce teacher plan limit
        $currentTeachers = Teacher::where('school_id', $school->id)->count();

        if ($currentTeachers >= $school->max_teachers) {
            return response()->json([
                'message' => 'Teacher limit reached for your current plan.',
                'max_allowed' => $school->max_teachers
            ], 403);
        }

        $teacher = Teacher::create([
            'school_id'  => $school->id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
        ]);

        AuditLogger::log($request->user(), 'teacher_created', 'teacher', $teacher->id);

        return response()->json($teacher, 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Teacher (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, Teacher $teacher)
    {
        if ($teacher->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($teacher);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Teacher (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Teacher $teacher)
    {
        if ($teacher->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|nullable|email',
            'phone'      => 'sometimes|nullable|string|max:20',
        ]);

        $teacher->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
        ]));

        AuditLogger::log($request->user(), 'teacher_updated', 'teacher', $teacher->id);

        return response()->json($teacher);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Teacher (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Teacher $teacher)
    {
        if ($teacher->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        AuditLogger::log($request->user(), 'teacher_deleted', 'teacher', $teacher->id);

        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully'
        ]);
    }
}