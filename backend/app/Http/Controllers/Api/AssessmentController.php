<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        return response()->json(
            Assessment::with([
                'teachingAssignment.subject',
                'teachingAssignment.teacher',
                'teachingAssignment.classRoom'
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'title'                  => 'required|string|max:255',
            'type'                   => 'required|in:quiz,homework,midterm,final,project,assignment',
            'max_score'              => 'required|numeric|min:0.01',
            'weight'                 => 'required|numeric|min:0.01',
            'assessment_date'        => 'required|date',
        ]);

        $schoolId = $request->user()->school_id;

        $assignment = TeachingAssignment::where('school_id', $schoolId)
            ->where('id', $request->teaching_assignment_id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'message' => 'Invalid teaching assignment'
            ], 400);
        }

        $assessment = Assessment::create([
            'teaching_assignment_id' => $assignment->id,
            'title'                  => $request->title,
            'type'                   => $request->type,
            'max_score'              => $request->max_score,
            'weight'                 => $request->weight,
            'assessment_date'        => $request->assessment_date,
        ]);

        return response()->json($assessment, 201);
    }

    public function show(Assessment $assessment)
    {
        return response()->json(
            $assessment->load([
                'teachingAssignment.subject',
                'teachingAssignment.teacher',
                'teachingAssignment.classRoom',
                'grades'
            ])
        );
    }

    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'title'           => 'sometimes|string|max:255',
            'type'            => 'sometimes|in:quiz,homework,midterm,final,project,assignment',
            'max_score'       => 'sometimes|numeric|min:0.01',
            'weight'          => 'sometimes|numeric|min:0.01',
            'assessment_date' => 'sometimes|date',
        ]);

        $assessment->update($request->only([
            'title',
            'type',
            'max_score',
            'weight',
            'assessment_date',
        ]));

        return response()->json($assessment);
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return response()->json([
            'message' => 'Assessment deleted successfully'
        ]);
    }
}