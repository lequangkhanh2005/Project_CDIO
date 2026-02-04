<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceJob;
use App\Models\JobHistory;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceJob::query()->with('technician');
        $user = $request->user();
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->query('technician_id'));
        } elseif ($user && $user->role === 'technician') {
            $query->where('technician_id', $user->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json([
            'data' => $query->orderBy('appointment_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:maintenance_jobs,code'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string'],
            'appointment_at' => ['nullable', 'date'],
            'resident_name' => ['required', 'string'],
            'resident_phone' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $job = MaintenanceJob::create([
            ...$data,
            'status' => $data['status'] ?? 'Moi',
        ]);

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Tao job',
            'note' => $job->title,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da tao job.',
            'data' => $job,
        ], 201);
    }

    public function show(MaintenanceJob $job)
    {
        $job->load(['technician', 'workLogs']);

        return response()->json([
            'data' => $job,
        ]);
    }

    public function update(Request $request, MaintenanceJob $job)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'location' => ['sometimes', 'string'],
            'appointment_at' => ['nullable', 'date'],
            'resident_name' => ['sometimes', 'string'],
            'resident_phone' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $job->update($data);

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Cap nhat job',
            'note' => $job->title,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da cap nhat job.',
            'data' => $job,
        ]);
    }

    public function destroy(MaintenanceJob $job)
    {
        $job->delete();

        return response()->json([
            'message' => 'Da xoa job.',
        ]);
    }

    public function updateStatus(Request $request, MaintenanceJob $job)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $job->status = $data['status'];
        $job->save();

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Cap nhat trang thai',
            'note' => $data['status'],
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da cap nhat trang thai.',
            'data' => $job,
        ]);
    }

    public function assign(Request $request, MaintenanceJob $job)
    {
        $data = $request->validate([
            'technician_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $job->technician_id = $data['technician_id'];
        $job->save();

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Giao ky thuat vien',
            'note' => 'Technician #' . $data['technician_id'],
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da giao ky thuat vien.',
            'data' => $job,
        ]);
    }

    public function storeWorklog(Request $request, MaintenanceJob $job)
    {
        $data = $request->validate([
            'hours' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $workLog = WorkLog::create([
            'maintenance_job_id' => $job->id,
            'technician_id' => $job->technician_id ?? optional($request->user())->id,
            'hours' => $data['hours'],
            'cost' => $data['cost'],
            'note' => $data['note'] ?? null,
            'logged_at' => now(),
        ]);

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Ghi worklog',
            'note' => $data['note'] ?? null,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da ghi worklog.',
            'data' => $workLog,
        ], 201);
    }

    public function complete(MaintenanceJob $job)
    {
        $job->status = 'Hoan thanh';
        $job->completed_at = now();
        $job->save();

        JobHistory::create([
            'maintenance_job_id' => $job->id,
            'action' => 'Hoan thanh job',
            'note' => $job->status,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'message' => 'Da hoan thanh cong viec.',
            'data' => $job,
        ]);
    }
}
