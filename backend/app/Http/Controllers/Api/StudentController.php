<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Notifications\SystemNotification;
use App\Services\AuditLogger;

class StudentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List Students (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $students = Student::where('school_id', $schoolId)
            ->with('section.classRoom')
            ->latest()
            ->get();

        return response()->json($students);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Student (Plan Enforced + Notification)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'nullable|email',
            'section_id' => 'required|exists:sections,id',
        ]);

        $school = $request->user()->school;

        /*
        |--------------------------------------------------------------------------
        | Enforce student plan limit
        |--------------------------------------------------------------------------
        */

        $currentStudents = Student::where('school_id', $school->id)->count();

        if ($school->max_students !== null && $currentStudents >= $school->max_students) {
            return response()->json([
                'message' => 'Student limit reached for your current plan.',
                'max_allowed' => $school->max_students
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Ensure section belongs to same school
        |--------------------------------------------------------------------------
        */

        $section = Section::where('school_id', $school->id)
            ->where('id', $request->section_id)
            ->first();

        if (!$section) {
            return response()->json([
                'message' => 'Invalid section for this school'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Create student
        |--------------------------------------------------------------------------
        */

        $student = Student::create([
            'school_id'  => $school->id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'section_id' => $request->section_id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send Notification
        |--------------------------------------------------------------------------
        */

        $request->user()->notify(
            new SystemNotification(
                "Student {$student->first_name} {$student->last_name} added successfully."
            )
        );

        AuditLogger::log($request->user(), 'student_created', 'student', $student->id);

        return response()->json($student, 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Student (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, Student $student)
    {
        if ($student->school_id !== $request->user()->school_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json(
            $student->load('section.classRoom')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Student (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Student $student)
    {
        if ($student->school_id !== $request->user()->school_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|nullable|email',
            'section_id' => 'sometimes|exists:sections,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate section belongs to school
        |--------------------------------------------------------------------------
        */

        if ($request->has('section_id')) {

            $section = Section::where('school_id', $request->user()->school_id)
                ->where('id', $request->section_id)
                ->first();

            if (!$section) {
                return response()->json([
                    'message' => 'Invalid section for this school'
                ], 400);
            }
        }

        $student->update($request->only([
            'first_name',
            'last_name',
            'email',
            'section_id',
        ]));

        AuditLogger::log($request->user(), 'student_updated', 'student', $student->id);

        return response()->json($student);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Student (School Scoped)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Student $student)
    {
        if ($student->school_id !== $request->user()->school_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        AuditLogger::log($request->user(), 'student_deleted', 'student', $student->id);

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully'
        ]);
    }
}