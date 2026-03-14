<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeachingAssignmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TeacherDashboardController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\ParentDashboardController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\BillingController;

/*
|--------------------------------------------------------------------------
| Public Routes (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:api')->group(function () {

    Route::post('/register-school', [AuthController::class, 'registerSchool']);

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

});

/*
|--------------------------------------------------------------------------
| Stripe Webhook (NO AUTH)
|--------------------------------------------------------------------------
*/

Route::post(
    '/stripe/webhook',
    [StripeWebhookController::class, 'handle']
);

// Additional billing webhook endpoint (public)
Route::post('/billing/webhook', [BillingController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','throttle:api'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','role:super_admin','throttle:api'])->group(function () {

    Route::get('/super-admin/schools', [SuperAdminController::class, 'schools']);
    Route::get('/super-admin/overview', [SuperAdminController::class, 'overview']);
    Route::get('/super-admin/total-schools', [DashboardController::class, 'totalSchools']);
    Route::get('/super-admin/active-subscriptions', [DashboardController::class, 'activeSubscriptions']);
    Route::get('/super-admin/trial-schools', [DashboardController::class, 'trialSchools']);
    Route::get('/super-admin/monthly-revenue', [DashboardController::class, 'monthlyRevenue']);
    Route::get('/super-admin/growth-analytics', [DashboardController::class, 'growthAnalytics']);

});

/*
|--------------------------------------------------------------------------
| School Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','role:school_admin','school.scope','check.subscription','throttle:api'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/analytics/overview', [AnalyticsController::class, 'overview']);

    /*
    |--------------------------------------------------------------------------
    | Academic Core
    |--------------------------------------------------------------------------
    */

    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('class-rooms', ClassRoomController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('teaching-assignments', TeachingAssignmentController::class);
    Route::apiResource('assessments', AssessmentController::class);
    Route::apiResource('grades', GradeController::class);

    /*
    |--------------------------------------------------------------------------
    | Finance
    |--------------------------------------------------------------------------
    */

    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('payments', PaymentController::class)->only(['store']);

    Route::get(
        '/dashboard/financial',
        [PaymentController::class, 'financialDashboard']
    );

    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */
    // Legacy StripePaymentController checkout (kept for compatibility)
    Route::post(
        '/billing/subscribe',
        [StripePaymentController::class, 'createCheckoutSession']
    );

    // New billing endpoints
    Route::post('/billing/checkout', [BillingController::class, 'checkout']);
    Route::get('/billing/subscription', [BillingController::class, 'subscription']);
    Route::post('/billing/cancel', [BillingController::class, 'cancel']);

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    Route::apiResource('attendances', AttendanceController::class);

    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    */

    Route::post(
        'class-rooms/{classRoom}/attach-subject',
        [ClassRoomController::class, 'attachSubject']
    );

    Route::post(
        'subjects/{subject}/attach-teacher',
        [SubjectController::class, 'attachTeacher']
    );

    /*
    |--------------------------------------------------------------------------
    | Academic Intelligence
    |--------------------------------------------------------------------------
    */

    Route::get(
        'teaching-assignments/{teachingAssignment}/ranking',
        [GradeController::class, 'classRanking']
    );

    Route::get(
        'students/{student}/results/{teachingAssignment}',
        [GradeController::class, 'studentResult']
    );

    Route::get(
        'students/{student}/cgpa',
        [GradeController::class, 'studentCgpa']
    );

    Route::get(
        'students/{student}/risk-analysis',
        [GradeController::class, 'riskAnalysis']
    );

    Route::get(
        'students/{student}/report-card/{academicYear}',
        [GradeController::class, 'reportCard']
    );

    Route::get(
        '/dashboard/analytics',
        [GradeController::class, 'dashboardAnalytics']
    );

    Route::get(
        '/dashboard/risk-summary',
        [GradeController::class, 'dashboardRiskSummary']
    );

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/files', [FileController::class, 'index']);
    Route::delete('/files/{file}', [FileController::class, 'destroy']);

});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','role:teacher','school.scope','check.subscription','throttle:api'])->group(function () {

    Route::get(
        '/teacher/dashboard',
        [TeacherDashboardController::class, 'dashboard']
    );

    Route::get(
        '/teacher/classes',
        [TeacherDashboardController::class, 'classes']
    );

    Route::get(
        '/teacher/attendance',
        [TeacherDashboardController::class, 'attendance']
    );

    Route::get(
        '/teacher/grades',
        [TeacherDashboardController::class, 'grades']
    );

});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','role:student','school.scope','check.subscription','throttle:api'])->group(function () {

    Route::get(
        '/student/dashboard',
        [StudentDashboardController::class, 'dashboard']
    );

    Route::get(
        '/student/report-cards',
        [StudentDashboardController::class, 'reportCards']
    );

    Route::get(
        '/student/attendance',
        [StudentDashboardController::class, 'attendance']
    );

    Route::get(
        '/student/results',
        [StudentDashboardController::class, 'results']
    );

    Route::get(
        '/student/notifications',
        [StudentDashboardController::class, 'notifications']
    );

});

/*
|--------------------------------------------------------------------------
| Parent Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','role:parent','school.scope','check.subscription','throttle:api'])->group(function () {

    Route::get(
        '/parent/dashboard',
        [ParentDashboardController::class, 'dashboard']
    );

});