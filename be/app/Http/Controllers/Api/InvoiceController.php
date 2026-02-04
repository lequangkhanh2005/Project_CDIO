<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Invoice::orderByDesc('issued_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:invoices,code'],
            'resident_id' => ['required', 'exists:residents,id'],
            'job_id' => ['nullable', 'exists:maintenance_jobs,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
            'issued_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::create([
            ...$data,
            'status' => $data['status'] ?? 'Cho thanh toan',
        ]);

        return response()->json([
            'message' => 'Đã tạo hóa đơn.',
            'data' => $invoice,
        ], 201);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'resident_id' => ['sometimes', 'exists:residents,id'],
            'job_id' => ['nullable', 'exists:maintenance_jobs,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string'],
            'issued_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $invoice->update($data);

        return response()->json([
            'message' => 'Đã cập nhật hóa đơn.',
            'data' => $invoice,
        ]);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->json([
            'message' => 'Đã xóa hóa đơn.',
        ]);
    }
}
