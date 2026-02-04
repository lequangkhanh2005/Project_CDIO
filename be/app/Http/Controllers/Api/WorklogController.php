<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class WorklogController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkLog::query();
        if ($request->filled('job_id')) {
            $query->where('maintenance_job_id', $request->query('job_id'));
        }
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->query('technician_id'));
        }

        return response()->json([
            'data' => $query->orderByDesc('logged_at')->get(),
        ]);
    }

    public function update(Request $request, WorkLog $worklog)
    {
        $data = $request->validate([
            'hours' => ['sometimes', 'numeric', 'min:0'],
            'cost' => ['sometimes', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $worklog->update($data);

        return response()->json([
            'message' => 'Đã cập nhật worklog.',
            'data' => $worklog,
        ]);
    }

    public function destroy(WorkLog $worklog)
    {
        $worklog->delete();

        return response()->json([
            'message' => 'Đã xóa worklog.',
        ]);
    }
}
