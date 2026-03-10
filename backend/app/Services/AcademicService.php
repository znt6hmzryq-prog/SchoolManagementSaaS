<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Student;

class AcademicService
{
    /*
    |--------------------------------------------------------------------------
    | Calculate CGPA For All Students (Bulk)
    |--------------------------------------------------------------------------
    */

    public function calculateAllStudentsCgpa($schoolId)
    {
        $grades = Grade::where('school_id', $schoolId)
            ->with('assessment')
            ->get();

        $studentData = [];

        foreach ($grades as $grade) {

            $assessment = $grade->assessment;

            if (!$assessment || $assessment->max_score == 0) {
                continue;
            }

            $studentId = $grade->student_id;

            $percentage = $grade->score / $assessment->max_score;
            $weightedScore = $percentage * $assessment->weight;

            if (!isset($studentData[$studentId])) {
                $studentData[$studentId] = [
                    'total_weight' => 0,
                    'total_weighted_score' => 0
                ];
            }

            $studentData[$studentId]['total_weight'] += $assessment->weight;
            $studentData[$studentId]['total_weighted_score'] += $weightedScore;
        }

        $cgpas = [];

        foreach ($studentData as $studentId => $data) {

            if ($data['total_weight'] == 0) {
                $cgpas[$studentId] = 0;
                continue;
            }

            $finalPercentage =
                ($data['total_weighted_score'] / $data['total_weight']) * 100;

            $cgpas[$studentId] = $this->mapPercentageToGpa($finalPercentage);
        }

        return $cgpas;
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate Single Student CGPA
    |--------------------------------------------------------------------------
    */

    public function calculateStudentCgpa($student, $schoolId)
    {
        $grades = Grade::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->with('assessment')
            ->get();

        $totalWeight = 0;
        $totalWeightedScore = 0;

        foreach ($grades as $grade) {

            $assessment = $grade->assessment;

            if (!$assessment || $assessment->max_score == 0) {
                continue;
            }

            $percentage = $grade->score / $assessment->max_score;
            $weightedScore = $percentage * $assessment->weight;

            $totalWeight += $assessment->weight;
            $totalWeightedScore += $weightedScore;
        }

        if ($totalWeight == 0) {
            return 0;
        }

        $finalPercentage =
            ($totalWeightedScore / $totalWeight) * 100;

        return $this->mapPercentageToGpa($finalPercentage);
    }

    /*
    |--------------------------------------------------------------------------
    | Risk Summary (Bulk)
    |--------------------------------------------------------------------------
    */

    public function calculateRiskSummary($schoolId)
    {
        $cgpas = $this->calculateAllStudentsCgpa($schoolId);

        $attendanceRecords = Attendance::where('school_id', $schoolId)->get();

        $attendanceData = [];

        foreach ($attendanceRecords as $record) {

            $studentId = $record->student_id;

            if (!isset($attendanceData[$studentId])) {
                $attendanceData[$studentId] = [
                    'total' => 0,
                    'present' => 0
                ];
            }

            $attendanceData[$studentId]['total']++;

            if ($record->status === 'present') {
                $attendanceData[$studentId]['present']++;
            }
        }

        $students = Student::where('school_id', $schoolId)->get();

        $summary = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'high_risk_students' => []
        ];

        foreach ($students as $student) {

            $cgpa = $cgpas[$student->id] ?? 0;

            $attendanceRate = 0;

            if (isset($attendanceData[$student->id])) {
                $data = $attendanceData[$student->id];
                $attendanceRate = $data['total'] > 0
                    ? ($data['present'] / $data['total']) * 100
                    : 0;
            }

            $riskScore = 0;

            // GPA Risk
            if ($cgpa < 2.0) $riskScore += 2;
            elseif ($cgpa < 3.0) $riskScore += 1;

            // Attendance Risk
            if ($attendanceRate < 60) $riskScore += 2;
            elseif ($attendanceRate < 75) $riskScore += 1;

            if ($riskScore >= 3) {
                $summary['high']++;
                $summary['high_risk_students'][] = [
                    'student_id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'cgpa' => $cgpa,
                    'attendance_rate' => round($attendanceRate, 2)
                ];
            }
            elseif ($riskScore == 2) {
                $summary['medium']++;
            }
            else {
                $summary['low']++;
            }
        }

        return $summary;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: Percentage → GPA Mapping
    |--------------------------------------------------------------------------
    */

    private function mapPercentageToGpa($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }
}