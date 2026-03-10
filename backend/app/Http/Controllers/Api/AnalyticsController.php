<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TeachingAssignment;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Services\AcademicService;

class AnalyticsController extends Controller
{
    protected $academicService;

    public function __construct(AcademicService $academicService)
    {
        $this->academicService = $academicService;
    }

    public function overview(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // 🔹 Get all students
        $students = Student::where('school_id', $schoolId)->get();

        // 🔹 Bulk CGPA calculation
        $cgpas = $this->academicService
            ->calculateAllStudentsCgpa($schoolId);

        $studentPerformance = [];

        foreach ($students as $student) {
            $studentPerformance[] = [
                'student_id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'cgpa' => $cgpas[$student->id] ?? 0
            ];
        }

        // Sort by CGPA
        usort($studentPerformance, fn($a, $b) =>
            $b['cgpa'] <=> $a['cgpa']
        );

        $schoolAverage = count($studentPerformance) > 0
            ? round(
                array_sum(array_column($studentPerformance, 'cgpa')) 
                / count($studentPerformance),
                2
            )
            : 0;

        $topStudents = array_slice($studentPerformance, 0, 5);
        $bottomStudents = array_slice(array_reverse($studentPerformance), 0, 5);

        // 🔹 Class Performance
        $classes = ClassRoom::where('school_id', $schoolId)->get();
        $classPerformance = [];

        foreach ($classes as $class) {

            $classStudents = $students->filter(function ($student) use ($class) {
                return $student->section 
                    && $student->section->class_room_id == $class->id;
            });

            $average = count($classStudents) > 0
                ? round(
                    array_sum(array_map(function ($student) use ($cgpas) {
                        return $cgpas[$student->id] ?? 0;
                    }, $classStudents->all())) 
                    / count($classStudents),
                    2
                )
                : 0;

            $classPerformance[] = [
                'class' => $class->name,
                'average_cgpa' => $average
            ];
        }

        usort($classPerformance, fn($a, $b) =>
            $b['average_cgpa'] <=> $a['average_cgpa']
        );

        // 🔹 Risk Distribution
        $riskSummary = $this->academicService
            ->calculateRiskSummary($schoolId);

        return response()->json([
            'school_average_cgpa' => $schoolAverage,
            'top_students' => $topStudents,
            'bottom_students' => $bottomStudents,
            'class_ranking' => $classPerformance,
            'risk_distribution' => [
                'low' => $riskSummary['low'],
                'medium' => $riskSummary['medium'],
                'high' => $riskSummary['high']
            ]
        ]);
    }
}