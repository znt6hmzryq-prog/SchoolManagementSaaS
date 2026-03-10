<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Services\AcademicService;

class ParentDashboardController extends Controller
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

        // Get children linked to this parent
        $students = Student::where('school_id', $schoolId)
            ->whereIn('id', function ($query) use ($user) {
                $query->select('student_id')
                      ->from('parent_student')
                      ->where('parent_id', $user->id);
            })
            ->with('section.classRoom')
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'message' => 'No children linked to this parent'
            ], 404);
        }

        $childrenData = [];

        foreach ($students as $student) {

            // CGPA
            $cgpa = $this->academicService
                ->calculateStudentCgpa($student, $schoolId);

            // Attendance %
            $attendanceRecords = Attendance::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->get();

            $attendanceRate = 0;

            if ($attendanceRecords->count() > 0) {
                $present = $attendanceRecords->where('status', 'present')->count();
                $attendanceRate = round(($present / $attendanceRecords->count()) * 100, 2);
            }

            // Pending invoices
            $pendingInvoices = Invoice::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('balance');

            // Risk logic
            $riskLevel = 'Low Risk';

            if ($cgpa < 2.0 || $attendanceRate < 60) {
                $riskLevel = 'High Risk';
            } elseif ($cgpa < 3.0 || $attendanceRate < 75) {
                $riskLevel = 'Medium Risk';
            }

            $childrenData[] = [
                'student' => $student->first_name . ' ' . $student->last_name,
                'class' => $student->section->classRoom->name,
                'cgpa' => $cgpa,
                'attendance_rate_percent' => $attendanceRate,
                'total_pending_balance' => $pendingInvoices,
                'risk_level' => $riskLevel
            ];
        }

        return response()->json([
            'parent' => $user->name,
            'children' => $childrenData
        ]);
    }
}