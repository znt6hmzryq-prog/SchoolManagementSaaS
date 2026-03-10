<?php

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;

class SubscriptionService
{
    /**
     * Plan Limits Configuration
     */
    protected array $planLimits = [
        'basic' => [
            'students' => 100,
            'teachers' => 10,
        ],
        'pro' => [
            'students' => 500,
            'teachers' => 50,
        ],
        'enterprise' => [
            'students' => null,     // null = unlimited
            'teachers' => null,
        ],
    ];

    /**
     * Get limits for a school
     */
    public function getPlanLimits(School $school): array
    {
        return $this->planLimits[$school->plan_type] ?? $this->planLimits['basic'];
    }

    /**
     * Check if student limit reached
     */
    public function studentLimitReached(School $school): bool
    {
        $limits = $this->getPlanLimits($school);

        if ($limits['students'] === null) {
            return false; // unlimited
        }

        $currentCount = Student::where('school_id', $school->id)->count();

        return $currentCount >= $limits['students'];
    }

    /**
     * Check if teacher limit reached
     */
    public function teacherLimitReached(School $school): bool
    {
        $limits = $this->getPlanLimits($school);

        if ($limits['teachers'] === null) {
            return false; // unlimited
        }

        $currentCount = Teacher::where('school_id', $school->id)->count();

        return $currentCount >= $limits['teachers'];
    }
}