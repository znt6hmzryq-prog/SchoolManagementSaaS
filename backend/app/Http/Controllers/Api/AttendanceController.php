<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\TeachingAssignment;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return response()->json(
            Attendance::with([
                'student',
                'teachingAssignment.subject',
                'teachingAssignment.teacher',
                'teachingAssignment.classRoom'
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'student_id'             => 'required|exists:students,id',
            'attendance_date'        => 'required|date',
            'status'                 => 'required|in:present,absent,late,excused',
        ]);

        $schoolId = $request->user()->school_id;

        // Validate teaching assignment
        $assignment = TeachingAssignment::where('school_id', $schoolId)
            ->where('id', $request->teaching_assignment_id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'message' => 'Invalid teaching assignment'
            ], 400);
        }

        // Validate student
        $student = Student::where('school_id', $schoolId)
            ->where('id', $request->student_id)
            ->first();

        if (!$student) {
            return response()->json([
                'message' => 'Invalid student'
            ], 400);
        }

        // Ensure student belongs to the same class as assignment
        $assignmentClassId = $assignment->class_room_id;

        if (!$student->section ||
            !$student->section->classRoom ||
            $student->section->classRoom->id !== $assignmentClassId) {

            return response()->json([
                'message' => 'Student does not belong to this class'
            ], 400);
        }

        // Prevent duplicate attendance (extra layer beyond DB constraint)
        $exists = Attendance::where('school_id', $schoolId)
            ->where('teaching_assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->where('attendance_date', $request->attendance_date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Attendance already recorded for this student on this date'
            ], 400);
        }

        $attendance = Attendance::create([
            'teaching_assignment_id' => $assignment->id,
            'student_id'             => $student->id,
            'attendance_date'        => $request->attendance_date,
            'status'                 => $request->status,
        ]);

        return response()->json($attendance, 201);
    }

    public function show(Attendance $attendance)
    {
        return response()->json(
            $attendance->load([
                'student',
                'teachingAssignment.subject',
                'teachingAssignment.teacher',
                'teachingAssignment.classRoom'
            ])
        );
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance deleted successfully'
        ]);
    }
}