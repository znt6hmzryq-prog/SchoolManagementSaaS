<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Invoice;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ List All Schools With Counts
    |--------------------------------------------------------------------------
    */
    public function schools()
    {
        $schools = School::withCount(['students', 'teachers'])->get();

        return response()->json($schools);
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Platform Overview Analytics
    |--------------------------------------------------------------------------
    */
    public function overview()
    {
        $totalSchools = School::count();
        $activeSchools = School::where('subscription_status', 'active')->count();
        $expiredSchools = School::whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', Carbon::now())
            ->count();

        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalRevenue = Invoice::sum('paid_amount');

        return response()->json([
            'total_schools' => $totalSchools,
            'active_schools' => $activeSchools,
            'expired_schools' => $expiredSchools,
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_revenue_collected' => $totalRevenue,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Upgrade School Plan
    |--------------------------------------------------------------------------
    */
    public function upgradePlan(Request $request, $schoolId)
    {
        $request->validate([
            'plan_type' => 'required|in:basic,pro,enterprise',
            'duration_months' => 'required|integer|min:1'
        ]);

        $school = School::findOrFail($schoolId);

        $school->plan_type = $request->plan_type;
        $school->subscription_status = 'active';
        $school->subscription_expires_at =
            Carbon::now()->addMonths($request->duration_months);

        $school->save();

        return response()->json([
            'message' => 'School plan upgraded successfully.',
            'school' => $school
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Suspend School
    |--------------------------------------------------------------------------
    */
    public function suspend($schoolId)
    {
        $school = School::findOrFail($schoolId);

        $school->subscription_status = 'cancelled';
        $school->save();

        return response()->json([
            'message' => 'School suspended successfully.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Reactivate School
    |--------------------------------------------------------------------------
    */
    public function reactivate($schoolId)
    {
        $school = School::findOrFail($schoolId);

        $school->subscription_status = 'active';

        if (!$school->subscription_expires_at ||
            $school->subscription_expires_at < Carbon::now()) {
            $school->subscription_expires_at = Carbon::now()->addMonth();
        }

        $school->save();

        return response()->json([
            'message' => 'School reactivated successfully.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Plan Usage Analytics Per School
    |--------------------------------------------------------------------------
    */
    public function planUsage($schoolId)
    {
        $school = School::findOrFail($schoolId);

        $studentsUsed = Student::where('school_id', $school->id)->count();
        $teachersUsed = Teacher::where('school_id', $school->id)->count();

        $studentLimit = $school->max_students ?? 0;
        $teacherLimit = $school->max_teachers ?? 0;

        $studentUsagePercent = $studentLimit > 0
            ? round(($studentsUsed / $studentLimit) * 100, 2)
            : 0;

        $teacherUsagePercent = $teacherLimit > 0
            ? round(($teachersUsed / $teacherLimit) * 100, 2)
            : 0;

        $upgradeRecommended =
            $studentUsagePercent >= 80 ||
            $teacherUsagePercent >= 80;

        return response()->json([
            'school' => $school->name,
            'plan_type' => $school->plan_type,

            'students_used' => $studentsUsed,
            'students_limit' => $studentLimit,
            'student_usage_percent' => $studentUsagePercent,
            'remaining_student_slots' => max(0, $studentLimit - $studentsUsed),

            'teachers_used' => $teachersUsed,
            'teachers_limit' => $teacherLimit,
            'teacher_usage_percent' => $teacherUsagePercent,
            'remaining_teacher_slots' => max(0, $teacherLimit - $teachersUsed),

            'upgrade_recommended' => $upgradeRecommended
        ]);
    }
}