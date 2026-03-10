<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\AcademicYear;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        return response()->json(
            ClassRoom::with(['academicYear', 'subjects'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Get active academic year for this school
        $activeYear = AcademicYear::where('school_id', $request->user()->school_id)
            ->where('is_active', true)
            ->first();

        if (!$activeYear) {
            return response()->json([
                'message' => 'No active academic year found'
            ], 400);
        }

        $classRoom = ClassRoom::create([
            'name' => $request->name,
            'academic_year_id' => $activeYear->id,
        ]);

        return response()->json($classRoom, 201);
    }

    public function show(ClassRoom $classRoom)
    {
        return response()->json(
            $classRoom->load(['academicYear', 'subjects'])
        );
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $classRoom->update($request->only('name'));

        return response()->json($classRoom);
    }

    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();

        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Attach Subject to ClassRoom
    |--------------------------------------------------------------------------
    */

    public function attachSubject(Request $request, ClassRoom $classRoom)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Ensure subject belongs to same school
        $subject = Subject::where('school_id', $request->user()->school_id)
            ->where('id', $request->subject_id)
            ->first();

        if (!$subject) {
            return response()->json([
                'message' => 'Invalid subject for this school'
            ], 400);
        }

        $classRoom->subjects()->syncWithoutDetaching([
            $request->subject_id
        ]);

        return response()->json([
            'message' => 'Subject attached successfully'
        ]);
    }
}