<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return response()->json(
            Section::with('classRoom')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'name' => 'required|string|max:10',
        ]);

        // Ensure class belongs to same school
        $classRoom = ClassRoom::where('school_id', $request->user()->school_id)
            ->where('id', $request->class_room_id)
            ->first();

        if (!$classRoom) {
            return response()->json([
                'message' => 'Invalid class for this school'
            ], 400);
        }

        $section = Section::create([
            'class_room_id' => $request->class_room_id,
            'name' => $request->name,
        ]);

        return response()->json($section, 201);
    }

    public function show(Section $section)
    {
        return response()->json($section->load('classRoom'));
    }

    public function update(Request $request, Section $section)
    {
        $section->update($request->only('name'));

        return response()->json($section);
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully'
        ]);
    }
}
