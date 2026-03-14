<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ReportCardMail;

class ReportCardController extends Controller
{
    public function show($studentId)
    {
        $user = auth()->user();

        $student = Student::where('school_id', $user->school_id)->with('section.classRoom')->findOrFail($studentId);

        $grades = Grade::where('student_id', $student->id)
            ->with(['assessment.teachingAssignment.subject'])
            ->get();

        $attendance = Attendance::where('student_id', $student->id)->get();

        return response()->json([
            'student' => $student,
            'grades' => $grades,
            'attendance' => [
                'total' => $attendance->count(),
                'present' => $attendance->where('status', 'present')->count(),
                'absent' => $attendance->where('status', 'absent')->count(),
            ],
        ]);
    }

    public function generate(Request $request, $studentId)
    {
        $user = auth()->user();

        $student = Student::where('school_id', $user->school_id)->with('section.classRoom')->findOrFail($studentId);

        // Collect grades grouped by subject
        $grades = Grade::where('student_id', $student->id)
            ->with(['assessment.teachingAssignment.subject'])
            ->get();

        $subjects = [];
        foreach ($grades as $g) {
            $subject = optional($g->assessment->teachingAssignment->subject)->name ?? 'General';
            if (!isset($subjects[$subject])) {
                $subjects[$subject] = ['scores' => [], 'count' => 0];
            }
            $subjects[$subject]['scores'][] = $g->score;
            $subjects[$subject]['count']++;
        }

        $subjectSummaries = [];
        foreach ($subjects as $name => $data) {
            $avg = count($data['scores']) ? array_sum($data['scores']) / count($data['scores']) : 0;
            $subjectSummaries[] = [
                'subject' => $name,
                'average' => round($avg, 2),
                'assessments' => $data['count'],
            ];
        }

        $attendance = Attendance::where('student_id', $student->id)->get();
        $attendanceSummary = [
            'total' => $attendance->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
        ];

        // Build prompt for AI
        $prompt = "Student: {$student->first_name} {$student->last_name}\n";
        $prompt .= "Class: " . optional($student->section->classRoom)->name . "\n";
        $prompt .= "\nSubject averages:\n";
        foreach ($subjectSummaries as $s) {
            $prompt .= "- {$s['subject']}: {$s['average']} (based on {$s['assessments']} assessments)\n";
        }
        $prompt .= "\nAttendance:\nTotal: {$attendanceSummary['total']}, Present: {$attendanceSummary['present']}, Absent: {$attendanceSummary['absent']}\n";
        $prompt .= "\nPlease write a short constructive teacher comment with positive feedback, areas for improvement, and recommendations.";

        $openaiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $aiComment = "";
        try {
            $resp = Http::withToken($openaiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => "You are an experienced school teacher writing constructive feedback for a student report card."],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 400,
                ]);

            if ($resp->successful()) {
                $payload = $resp->json();
                $aiComment = $payload['choices'][0]['message']['content'] ?? trim($resp->body());
            } else {
                Log::warning('OpenAI request failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI call error: ' . $e->getMessage());
        }

        // Prepare data for PDF
        $data = [
            'school' => config('app.name'),
            'student' => $student,
            'subjects' => $subjectSummaries,
            'attendance' => $attendanceSummary,
            'ai_comment' => $aiComment,
            'teacher' => $user,
            'date' => now()->toDateString(),
        ];

        // Generate PDF
        try {
            $pdf = Pdf::loadView('report-card', $data)->setPaper('A4');
            $pdfContent = $pdf->output();
            $fileName = 'report_card_' . $student->id . '.pdf';
            Storage::put('public/report_cards/' . $fileName, $pdfContent);
            $url = url('/storage/report_cards/' . $fileName);
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate PDF'], 500);
        }

        return response()->json([
            'pdf_url' => $url,
            'ai_comment' => $aiComment,
        ]);
    }

    public function email(Request $request, $studentId)
    {
        $user = auth()->user();

        $student = Student::where('school_id', $user->school_id)->with('section.classRoom')->findOrFail($studentId);

        $recipient = $student->email ?? null;
        if (!$recipient) {
            return response()->json(['message' => 'No parent/student email available to send to'], 422);
        }

        // Reuse generate logic to create PDF and AI comment
        $generateResp = $this->generate($request, $studentId);
        if ($generateResp->getStatusCode() !== 200) {
            return response()->json(['message' => 'Failed to generate report card'], 500);
        }
        $json = $generateResp->getData();
        $filePath = storage_path('app/public/report_cards/report_card_' . $student->id . '.pdf');

        Mail::to($recipient)->send(new ReportCardMail($student, $filePath));

        return response()->json(['message' => 'Report card emailed successfully']);
    }
}
