<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Assessment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\AcademicService;
use App\Services\AuditLogger;

class GradeController extends Controller
{
    protected $academicService;

    public function __construct(AcademicService $academicService)
    {
        $this->academicService = $academicService;
    }

    public function index()
    {
        return response()->json(
            Grade::with([
                'student',
                'assessment.teachingAssignment.subject',
                'assessment.teachingAssignment.teacher',
                'assessment.teachingAssignment.classRoom'
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'student_id'    => 'required|exists:students,id',
            'score'         => 'required|numeric|min:0',
        ]);

        $schoolId = $request->user()->school_id;

        $assessment = Assessment::where('school_id', $schoolId)
            ->where('id', $request->assessment_id)
            ->with('teachingAssignment')
            ->first();

        if (!$assessment) {
            return response()->json(['message' => 'Invalid assessment'], 400);
        }

        $student = Student::where('school_id', $schoolId)
            ->where('id', $request->student_id)
            ->with('section.classRoom')
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Invalid student'], 400);
        }

        $assignmentClassId = $assessment->teachingAssignment->class_room_id;

        if (
            !$student->section ||
            !$student->section->classRoom ||
            $student->section->classRoom->id !== $assignmentClassId
        ) {
            return response()->json([
                'message' => 'Student does not belong to this class'
            ], 400);
        }

        if ($request->score > $assessment->max_score) {
            return response()->json([
                'message' => 'Score cannot exceed maximum score'
            ], 400);
        }

        $exists = Grade::where('school_id', $schoolId)
            ->where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Grade already recorded for this student'
            ], 400);
        }

        $grade = Grade::create([
            'assessment_id' => $assessment->id,
            'student_id'    => $student->id,
            'score'         => $request->score,
        ]);

        AuditLogger::log($request->user(), 'grade_added', 'grade', $grade->id);

        return response()->json($grade, 201);
    }

    public function show(Grade $grade)
    {
        return response()->json(
            $grade->load([
                'student',
                'assessment.teachingAssignment.subject',
                'assessment.teachingAssignment.teacher',
                'assessment.teachingAssignment.classRoom'
            ])
        );
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'score' => 'required|numeric|min:0',
        ]);

        if ($request->score > $grade->assessment->max_score) {
            return response()->json([
                'message' => 'Score cannot exceed maximum score'
            ], 400);
        }

        $grade->update([
            'score' => $request->score
        ]);

        AuditLogger::log($request->user(), 'grade_updated', 'grade', $grade->id);

        return response()->json($grade);
    }

    public function destroy(Grade $grade)
    {
        AuditLogger::log(request()->user(), 'grade_deleted', 'grade', $grade->id);

        $grade->delete();

        return response()->json([
            'message' => 'Grade deleted successfully'
        ]);
    }

    // 🔥 Optimized Student CGPA (Single Student)
    public function studentCgpa($studentId)
    {
        $schoolId = request()->user()->school_id;

        $student = Student::where('school_id', $schoolId)
            ->where('id', $studentId)
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Invalid student'], 400);
        }

        $cgpa = $this->academicService
            ->calculateStudentCgpa($student, $schoolId);

        return response()->json([
            'student' => $student->first_name . ' ' . $student->last_name,
            'overall_cgpa' => $cgpa
        ]);
    }

    // 📊 Optimized Dashboard Analytics
    public function dashboardAnalytics()
    {
        $schoolId = request()->user()->school_id;

        $students = \App\Models\Student::where('school_id', $schoolId)->get();
        $teachers = \App\Models\Teacher::where('school_id', $schoolId)->count();
        $subjects = \App\Models\Subject::where('school_id', $schoolId)->count();

        $cgpas = $this->academicService
            ->calculateAllStudentsCgpa($schoolId);

        $studentPerformance = [];

        foreach ($students as $student) {
            $cgpa = $cgpas[$student->id] ?? 0;

            $studentPerformance[] = [
                'student_id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'cgpa' => $cgpa
            ];
        }

        usort($studentPerformance, fn($a, $b) =>
            $b['cgpa'] <=> $a['cgpa']
        );

        return response()->json([
            'total_students' => $students->count(),
            'total_teachers' => $teachers,
            'total_subjects' => $subjects,
            'top_students' => array_slice($studentPerformance, 0, 5),
            'bottom_students' => array_slice(array_reverse($studentPerformance), 0, 5),
        ]);
    }

    // 📊 Dashboard Risk Summary (Bulk)
    public function dashboardRiskSummary()
    {
        $schoolId = request()->user()->school_id;

        $summary = $this->academicService
            ->calculateRiskSummary($schoolId);

        return response()->json([
            'total_students' =>
                $summary['low'] + $summary['medium'] + $summary['high'],
            'low_risk' => $summary['low'],
            'medium_risk' => $summary['medium'],
            'high_risk' => $summary['high'],
            'high_risk_students' => $summary['high_risk_students']
        ]);
    }
}