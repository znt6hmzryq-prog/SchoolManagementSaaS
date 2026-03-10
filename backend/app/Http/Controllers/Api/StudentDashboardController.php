<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TeachingAssignment;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Invoice;
use Carbon\Carbon;
use App\Services\AcademicService;

class StudentDashboardController extends Controller
{
    protected $academicService;

    public function __construct(AcademicService $academicService)
    {
        $this->academicService = $academicService;
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        // 🔹 Find Student Profile
        $student = Student::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->with('section.classRoom')
            ->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student profile not found'
            ], 404);
        }

        // 🔹 Subjects
        $assignments = TeachingAssignment::where('school_id', $schoolId)
            ->where('class_room_id', $student->section->class_room_id)
            ->with('subject')
            ->get();

        $subjects = $assignments->map(function ($a) {
            return $a->subject->name;
        });

        // 🔹 CGPA
        $cgpa = $this->academicService
            ->calculateStudentCgpa($student, $schoolId);

        // 🔹 Attendance %
        $attendanceRecords = Attendance::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->get();

        $attendanceRate = 0;

        if ($attendanceRecords->count() > 0) {
            $present = $attendanceRecords->where('status', 'present')->count();
            $attendanceRate = round(($present / $attendanceRecords->count()) * 100, 2);
        }

        // 🔹 Pending Invoices
        $pendingInvoices = Invoice::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'partial'])
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->id,
                    'balance' => $invoice->balance,
                    'due_date' => $invoice->due_date
                ];
            });

        // 🔹 Risk Level
        $riskLevel = 'Low Risk';

        if ($cgpa < 2.0 || $attendanceRate < 60) {
            $riskLevel = 'High Risk';
        } elseif ($cgpa < 3.0 || $attendanceRate < 75) {
            $riskLevel = 'Medium Risk';
        }

        return response()->json([
            'student' => $student->first_name . ' ' . $student->last_name,
            'class' => $student->section->classRoom->name,
            'subjects' => $subjects,
            'cgpa' => $cgpa,
            'attendance_rate_percent' => $attendanceRate,
            'pending_invoices' => $pendingInvoices,
            'risk_level' => $riskLevel
        ]);
    }

    public function reportCards(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $student = Student::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $grades = \App\Models\Grade::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->with('assessment.subject')
            ->get()
            ->groupBy('assessment.subject.name');

        return response()->json(['report_cards' => $grades]);
    }

    public function attendance(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $student = Student::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $attendance = Attendance::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->get();

        return response()->json(['attendance' => $attendance]);
    }

    public function results(Request $request)
    {
        // Similar to report cards
        return $this->reportCards($request);
    }

    public function notifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->get();

        return response()->json(['notifications' => $notifications]);
    }
}