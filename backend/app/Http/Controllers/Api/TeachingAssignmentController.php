<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeachingAssignment;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachingAssignmentController extends Controller
{
    public function index()
    {
        return response()->json(
            TeachingAssignment::with([
                'academicYear',
                'classRoom',
                'subject',
                'teacher'
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_room_id'    => 'required|exists:class_rooms,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,id',
        ]);

        $schoolId = $request->user()->school_id;

        // Validate academic year
        $academicYear = AcademicYear::where('school_id', $schoolId)
            ->where('id', $request->academic_year_id)
            ->first();

        if (!$academicYear) {
            return response()->json(['message' => 'Invalid academic year'], 400);
        }

        // Validate class
        $classRoom = ClassRoom::where('school_id', $schoolId)
            ->where('id', $request->class_room_id)
            ->first();

        if (!$classRoom) {
            return response()->json(['message' => 'Invalid class room'], 400);
        }

        // Ensure class belongs to academic year
        if ($classRoom->academic_year_id != $academicYear->id) {
            return response()->json([
                'message' => 'Class does not belong to this academic year'
            ], 400);
        }

        // Validate subject
        $subject = Subject::where('school_id', $schoolId)
            ->where('id', $request->subject_id)
            ->first();

        if (!$subject) {
            return response()->json(['message' => 'Invalid subject'], 400);
        }

        // Ensure subject is attached to class
        if (!$classRoom->subjects()->where('subjects.id', $subject->id)->exists()) {
            return response()->json([
                'message' => 'Subject not assigned to this class'
            ], 400);
        }

        // Validate teacher
        $teacher = Teacher::where('school_id', $schoolId)
            ->where('id', $request->teacher_id)
            ->first();

        if (!$teacher) {
            return response()->json(['message' => 'Invalid teacher'], 400);
        }

        // Ensure teacher is attached to subject
        if (!$subject->teachers()->where('teachers.id', $teacher->id)->exists()) {
            return response()->json([
                'message' => 'Teacher not assigned to this subject'
            ], 400);
        }

        $assignment = TeachingAssignment::create([
            'academic_year_id' => $academicYear->id,
            'class_room_id'    => $classRoom->id,
            'subject_id'       => $subject->id,
            'teacher_id'       => $teacher->id,
        ]);

        return response()->json($assignment, 201);
    }

    public function show(TeachingAssignment $teachingAssignment)
    {
        return response()->json(
            $teachingAssignment->load([
                'academicYear',
                'classRoom',
                'subject',
                'teacher'
            ])
        );
    }

    public function destroy(TeachingAssignment $teachingAssignment)
    {
        $teachingAssignment->delete();

        return response()->json([
            'message' => 'Teaching assignment deleted successfully'
        ]);
    }
}