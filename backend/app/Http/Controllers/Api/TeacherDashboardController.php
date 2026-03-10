<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeachingAssignment;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Services\AcademicService;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
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

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Map Logged-in User → Teacher Profile
        |--------------------------------------------------------------------------
        */

        $teacher = Teacher::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher profile not found'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Fetch Teaching Assignments
        |--------------------------------------------------------------------------
        */

        $assignments = TeachingAssignment::where('school_id', $schoolId)
            ->where('teacher_id', $teacher->id)
            ->with(['subject', 'classRoom'])
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'message' => 'No classes assigned'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Build Analytics Per Class
        |--------------------------------------------------------------------------
        */

        $classAnalytics = [];

        // Bulk CGPA calculation once (performance optimization)
        $cgpas = $this->academicService
            ->calculateAllStudentsCgpa($schoolId);

        foreach ($assignments as $assignment) {

            // Students in this class
            $students = Student::where('school_id', $schoolId)
                ->whereHas('section', function ($q) use ($assignment) {
                    $q->where('class_room_id', $assignment->class_room_id);
                })
                ->get();

            $studentData = [];

            foreach ($students as $student) {

                $cgpa = $cgpas[$student->id] ?? 0;

                $studentData[] = [
                    'student_id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'cgpa' => $cgpa
                ];
            }

            // Sort students by CGPA
            usort($studentData, fn($a, $b) => $b['cgpa'] <=> $a['cgpa']);

            $topStudents = array_slice($studentData, 0, 3);
            $bottomStudents = array_slice(array_reverse($studentData), 0, 3);

            // Class average CGPA
            $average = count($studentData) > 0
                ? round(
                    array_sum(array_column($studentData, 'cgpa')) 
                    / count($studentData),
                    2
                )
                : 0;

            // Attendance today (based on created_at date)
            $todayAttendance = Attendance::where('school_id', $schoolId)
                ->whereDate('created_at', Carbon::today())
                ->whereIn('student_id', $students->pluck('id'))
                ->count();

            // Upcoming assessments
            $upcomingAssessments = Assessment::where('teaching_assignment_id', $assignment->id)
                ->whereDate('date', '>=', Carbon::today())
                ->orderBy('date')
                ->take(3)
                ->get(['title', 'type', 'date']);

            $classAnalytics[] = [
                'subject' => $assignment->subject->name,
                'class' => $assignment->classRoom->name,
                'total_students' => $students->count(),
                'class_average_cgpa' => $average,
                'top_students' => $topStudents,
                'bottom_students' => $bottomStudents,
                'attendance_records_today' => $todayAttendance,
                'upcoming_assessments' => $upcomingAssessments
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'teacher' => $user->name,
            'classes' => $classAnalytics
        ]);
    }

    public function classes(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $teacher = Teacher::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $assignments = TeachingAssignment::where('school_id', $schoolId)
            ->where('teacher_id', $teacher->id)
            ->with('classRoom', 'subject')
            ->get();

        return response()->json(['classes' => $assignments]);
    }

    public function attendance(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $teacher = Teacher::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        // Get attendance for classes taught by teacher
        $attendance = Attendance::where('school_id', $schoolId)
            ->whereHas('student.section', function ($q) use ($teacher) {
                $q->whereHas('classRoom.teachingAssignments', function ($q2) use ($teacher) {
                    $q2->where('teacher_id', $teacher->id);
                });
            })
            ->with('student')
            ->get();

        return response()->json(['attendance' => $attendance]);
    }

    public function grades(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $teacher = Teacher::where('school_id', $schoolId)
            ->where('email', $user->email)
            ->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $grades = \App\Models\Grade::where('school_id', $schoolId)
            ->whereHas('assessment.teachingAssignment', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with('student', 'assessment')
            ->get();

        return response()->json(['grades' => $grades]);
    }
}