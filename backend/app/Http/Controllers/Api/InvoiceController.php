<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StudentFeeAssignment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        return response()->json(
            Invoice::where('school_id', $schoolId)
                ->with('student')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_fee_assignment_id' => 'required|exists:student_fee_assignments,id',
            'due_date' => 'required|date'
        ]);

        $schoolId = $request->user()->school_id;

        $assignment = StudentFeeAssignment::where('school_id', $schoolId)
            ->where('id', $request->student_fee_assignment_id)
            ->with('student', 'feeStructure')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Invalid fee assignment'], 400);
        }

        $amount = $assignment->custom_amount 
            ?? $assignment->feeStructure->amount;

        $invoice = Invoice::create([
            'school_id' => $schoolId,
            'student_id' => $assignment->student_id,
            'student_fee_assignment_id' => $assignment->id,
            'amount' => $amount,
            'paid_amount' => 0,
            'balance' => $amount,
            'due_date' => Carbon::parse($request->due_date),
            'status' => 'pending'
        ]);

        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice)
    {
        return response()->json(
            $invoice->load('student', 'payments')
        );
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }
}