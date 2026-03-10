<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return response()->json(AcademicYear::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        // If new year is set active → deactivate others
        if ($request->is_active) {
            AcademicYear::where('school_id', $request->user()->school_id)
                ->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_active'  => $request->is_active ?? false,
        ]);

        return response()->json($academicYear, 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json($academicYear);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name'       => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date'   => 'sometimes|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        if ($request->is_active) {
            AcademicYear::where('school_id', $request->user()->school_id)
                ->update(['is_active' => false]);
        }

        $academicYear->update($request->only([
            'name',
            'start_date',
            'end_date',
            'is_active',
        ]));

        return response()->json($academicYear);
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return response()->json([
            'message' => 'Academic Year deleted successfully'
        ]);
    }
}
