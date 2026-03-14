<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    public function classPerformance(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $rows = DB::table('grades')
            ->join('assessments', 'grades.assessment_id', '=', 'assessments.id')
            ->join('teaching_assignments', 'assessments.teaching_assignment_id', '=', 'teaching_assignments.id')
            ->join('subjects', 'teaching_assignments.subject_id', '=', 'subjects.id')
            ->where('teaching_assignments.school_id', $schoolId)
            ->selectRaw('subjects.name as name, AVG(grades.score) as average')
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        $subjects = $rows->map(function ($r) {
            return ['name' => $r->name, 'average' => $r->average !== null ? round((float)$r->average, 2) : null];
        });

        return response()->json(['subjects' => $subjects]);
    }

    public function studentTrends(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $rows = DB::table('grades')
            ->join('assessments', 'grades.assessment_id', '=', 'assessments.id')
            ->join('teaching_assignments', 'assessments.teaching_assignment_id', '=', 'teaching_assignments.id')
            ->where('teaching_assignments.school_id', $schoolId)
            ->selectRaw("to_char(assessments.assessment_date, 'Mon YYYY') as month, AVG(grades.score) as average, MIN(assessments.assessment_date) as sort_date")
            ->groupBy('month')
            ->orderBy('sort_date')
            ->get();

        $trend = $rows->map(function ($r) {
            return ['month' => $r->month, 'average' => $r->average !== null ? round((float)$r->average, 2) : null];
        });

        return response()->json(['trend' => $trend]);
    }

    public function attendanceTrends(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $rows = DB::table('attendances')
            ->where('school_id', $schoolId)
            ->selectRaw("attendance_date::date as date, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present")
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get();

        $attendance = $rows->map(function ($r) {
            return ['date' => $r->date, 'present' => (int)$r->present];
        });

        return response()->json(['attendance' => $attendance]);
    }

    public function aiInsights(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        // Build summaries
        $performance = DB::table('grades')
            ->join('assessments', 'grades.assessment_id', '=', 'assessments.id')
            ->join('teaching_assignments', 'assessments.teaching_assignment_id', '=', 'teaching_assignments.id')
            ->join('subjects', 'teaching_assignments.subject_id', '=', 'subjects.id')
            ->where('teaching_assignments.school_id', $schoolId)
            ->selectRaw('subjects.name as subject, AVG(grades.score) as average')
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        $attendance = DB::table('attendances')
            ->where('school_id', $schoolId)
            ->selectRaw('attendance_date::date as date, SUM(CASE WHEN status = \"present\" THEN 1 ELSE 0 END) as present')
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();

        $gradeDistribution = DB::table('grades')
            ->join('assessments', 'grades.assessment_id', '=', 'assessments.id')
            ->join('teaching_assignments', 'assessments.teaching_assignment_id', '=', 'teaching_assignments.id')
            ->where('teaching_assignments.school_id', $schoolId)
            ->selectRaw('FLOOR(grades.score / 10) * 10 as bucket, COUNT(*) as count')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $system = "You are an educational analytics assistant. Analyze the class performance and provide concise insights for teachers.";

        $userPrompt = "Class Performance Summary:\n" . json_encode($performance) . "\n\nAttendance Summary (recent):\n" . json_encode($attendance) . "\n\nGrade Distribution:\n" . json_encode($gradeDistribution) . "\n\nProvide 3-6 short insights, each one sentence.";

        $openaiKey = env('OPENAI_API_KEY');
        if (! $openaiKey) {
            return response()->json(['message' => 'OpenAI API key not configured'], 500);
        }

        try {
            $resp = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens' => 400,
            ]);

            if ($resp->failed()) {
                Log::error('OpenAI analytics request failed', ['status' => $resp->status(), 'body' => $resp->body()]);
                return response()->json(['message' => 'AI service error'], 500);
            }

            $body = $resp->json();
            $content = $body['choices'][0]['message']['content'] ?? ($body['choices'][0]['text'] ?? null);
            $insights = [];
            if ($content) {
                // Split into lines and keep non-empty
                $lines = preg_split('/\r?\n/', trim($content));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== '') $insights[] = $line;
                }
            }

            return response()->json(['insights' => $insights]);

        } catch (\Exception $e) {
            Log::error('AI analytics error: ' . $e->getMessage());
            return response()->json(['message' => 'AI analytics failed'], 500);
        }
    }
}
