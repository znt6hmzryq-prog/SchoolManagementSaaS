<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function totalSchools()
    {
        $total = School::count();
        return response()->json(['total_schools' => $total]);
    }

    public function activeSubscriptions()
    {
        $active = School::where('subscription_status', 'active')->count();
        return response()->json(['active_subscriptions' => $active]);
    }

    public function trialSchools()
    {
        $trial = School::where('plan_type', 'trial')->count();
        return response()->json(['trial_schools' => $trial]);
    }

    public function monthlyRevenue()
    {
        $revenue = Invoice::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('paid_amount');
        return response()->json(['monthly_revenue' => $revenue]);
    }

    public function growthAnalytics()
    {
        // Simple growth: schools created this month vs last month
        $thisMonth = School::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $lastMonth = School::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $growth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
        return response()->json(['growth_percentage' => round($growth, 2)]);
    }
}