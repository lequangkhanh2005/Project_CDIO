<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = JobHistory::query()->orderByDesc('created_at');
        if ($request->filled('job_id')) {
            $query->where('maintenance_job_id', $request->query('job_id'));
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }
}
