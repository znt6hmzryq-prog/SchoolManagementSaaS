<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;

class AITeacherAssistantController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->validate([
            'prompt' => 'required|string|max:2000',
            'class_id' => 'nullable|integer|exists:class_rooms,id',
        ]);

        $teacher = $request->user();

        if (! $teacher || $teacher->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure teacher belongs to a school
        $schoolId = $teacher->school_id;
        if (! $schoolId) {
            return response()->json(['message' => 'Teacher not associated with a school'], 403);
        }

        $classInfo = null;
        $studentPerformanceSummary = '';

        if (! empty($data['class_id'])) {
            $class = ClassRoom::where('id', $data['class_id'])
                ->where('school_id', $schoolId)
                ->first();

            if (! $class) {
                return response()->json(['message' => 'Class not found or not in your school'], 404);
            }

            $classInfo = [
                'id' => $class->id,
                'name' => $class->name ?? '',
                'academic_year_id' => $class->academic_year_id ?? null,
            ];

            // Gather student ids for the class via sections (scoped to school)
            $sectionIds = $class->sections()->pluck('id')->toArray();
            $students = Student::whereIn('section_id', $sectionIds)
                ->where('school_id', $schoolId)
                ->get();
            $studentIds = $students->pluck('id')->toArray();

            // Compute simple performance summary (scoped to school)
            $avgScore = null;
            if (! empty($studentIds)) {
                $avgScore = Grade::whereIn('student_id', $studentIds)
                    ->where('school_id', $schoolId)
                    ->avg('score');

                $totalAttendance = Attendance::whereIn('student_id', $studentIds)
                    ->where('school_id', $schoolId)
                    ->count();

                $presentCount = Attendance::whereIn('student_id', $studentIds)
                    ->where('school_id', $schoolId)
                    ->where('status', 'present')
                    ->count();

                $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : null;

                $studentPerformanceSummary = "Students: " . count($studentIds) . ", Average Score: " . ($avgScore !== null ? round($avgScore,2) : 'N/A') . ", Attendance Rate: " . ($attendanceRate !== null ? $attendanceRate . '%' : 'N/A');
            } else {
                $studentPerformanceSummary = "No students found in this class.";
            }
        } else {
            $studentPerformanceSummary = "No class specified.";
        }

        $systemPrompt = "You are an expert teacher assistant helping teachers create lessons, quizzes and analyze student performance.";

        $userPrompt = "Teacher Request:\n" . $data['prompt'] . "\n\nClass Data:\n" . json_encode($classInfo) . "\n\nStudent Performance:\n" . $studentPerformanceSummary . "\n\nGenerate helpful teaching content.";

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
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens' => 800,
            ]);

            if ($resp->failed()) {
                Log::error('OpenAI request failed', ['status' => $resp->status(), 'body' => $resp->body()]);
                return response()->json(['message' => 'AI service error'], 500);
            }

            $body = $resp->json();

            $answer = null;
            if (isset($body['choices'][0]['message']['content'])) {
                $answer = $body['choices'][0]['message']['content'];
            } elseif (isset($body['choices'][0]['text'])) {
                $answer = $body['choices'][0]['text'];
            }

            return response()->json(['answer' => $answer]);

        } catch (\Exception $e) {
            Log::error('AI assistant error: ' . $e->getMessage());
            return response()->json(['message' => 'AI assistant failed'], 500);
        }
    }
}
