<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(
            Subject::with(['academicYear', 'teachers'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50',
        ]);

        // Ensure academic year belongs to same school
        $academicYear = AcademicYear::where('school_id', $request->user()->school_id)
            ->where('id', $request->academic_year_id)
            ->first();

        if (!$academicYear) {
            return response()->json([
                'message' => 'Invalid academic year for this school'
            ], 400);
        }

        $subject = Subject::create([
            'academic_year_id' => $request->academic_year_id,
            'name'             => $request->name,
            'code'             => $request->code,
        ]);

        return response()->json($subject, 201);
    }

    public function show(Subject $subject)
    {
        return response()->json(
            $subject->load(['academicYear', 'teachers'])
        );
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|nullable|string|max:50',
        ]);

        $subject->update($request->only([
            'name',
            'code',
        ]));

        return response()->json($subject);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Attach Teacher to Subject
    |--------------------------------------------------------------------------
    */

    public function attachTeacher(Request $request, Subject $subject)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Ensure teacher belongs to same school
        $teacher = Teacher::where('school_id', $request->user()->school_id)
            ->where('id', $request->teacher_id)
            ->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'Invalid teacher for this school'
            ], 400);
        }

        $subject->teachers()->syncWithoutDetaching([
            $request->teacher_id
        ]);

        return response()->json([
            'message' => 'Teacher attached successfully'
        ]);
    }
}