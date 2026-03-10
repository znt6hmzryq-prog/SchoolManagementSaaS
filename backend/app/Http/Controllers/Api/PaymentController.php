<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use App\Services\AuditLogger;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string'
        ]);

        $schoolId = $request->user()->school_id;

        $invoice = Invoice::where('school_id', $schoolId)
            ->where('id', $request->invoice_id)
            ->first();

        if (!$invoice) {
            return response()->json(['message' => 'Invalid invoice'], 400);
        }

        if ($request->amount_paid > $invoice->balance) {
            return response()->json([
                'message' => 'Payment exceeds remaining balance'
            ], 400);
        }

        $payment = Payment::create([
            'school_id' => $schoolId,
            'invoice_id' => $invoice->id,
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'paid_at' => now()
        ]);

        $invoice->paid_amount += $request->amount_paid;
        $invoice->balance = $invoice->amount - $invoice->paid_amount;

        if ($invoice->balance == 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partial';
        }

        $invoice->save();

        AuditLogger::log($request->user(), 'payment_recorded', 'payment', $payment->id);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'invoice' => $invoice
        ]);
    }

    // 📊 Financial Dashboard
    public function financialDashboard()
    {
        $schoolId = request()->user()->school_id;

        $invoices = Invoice::where('school_id', $schoolId)->get();
        $payments = Payment::where('school_id', $schoolId)->get();

        $totalRevenue = $payments->sum('amount_paid');
        $totalOutstanding = $invoices->sum('balance');

        $totalInvoices = $invoices->count();
        $paidInvoices = $invoices->where('status', 'paid')->count();
        $pendingInvoices = $invoices->whereIn('status', ['pending','partial'])->count();

        $collectionRate = $invoices->sum('amount') > 0
            ? round(($totalRevenue / $invoices->sum('amount')) * 100, 2)
            : 0;

        $monthlyRevenue = [];

        foreach ($payments as $payment) {
            $month = Carbon::parse($payment->paid_at)->format('Y-m');

            if (!isset($monthlyRevenue[$month])) {
                $monthlyRevenue[$month] = 0;
            }

            $monthlyRevenue[$month] += $payment->amount_paid;
        }

        return response()->json([
            'total_revenue_collected' => $totalRevenue,
            'total_outstanding_balance' => $totalOutstanding,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'collection_rate_percent' => $collectionRate,
            'monthly_revenue' => $monthlyRevenue
        ]);
    }
}