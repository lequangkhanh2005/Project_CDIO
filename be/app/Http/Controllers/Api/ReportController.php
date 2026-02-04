<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\MaintenanceJob;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $jobsQuery = MaintenanceJob::query();
        $worklogQuery = WorkLog::query();
        $invoiceQuery = Invoice::query();

        if ($from) {
            $jobsQuery->whereDate('created_at', '>=', $from);
            $worklogQuery->whereDate('logged_at', '>=', $from);
            $invoiceQuery->whereDate('issued_at', '>=', $from);
        }
        if ($to) {
            $jobsQuery->whereDate('created_at', '<=', $to);
            $worklogQuery->whereDate('logged_at', '<=', $to);
            $invoiceQuery->whereDate('issued_at', '<=', $to);
        }

        $jobsCompletedQuery = (clone $jobsQuery)->where('status', 'Hoan thanh');
        $invoicePaidQuery = (clone $invoiceQuery)->where('status', 'Da thanh toan');

        return response()->json([
            'data' => [
                'jobs_total' => $jobsQuery->count(),
                'jobs_completed' => $jobsCompletedQuery->count(),
                'worklog_cost_total' => $worklogQuery->sum('cost'),
                'invoice_total' => $invoiceQuery->sum('amount'),
                'invoice_paid' => $invoicePaidQuery->sum('amount'),
            ],
        ]);
    }
}
