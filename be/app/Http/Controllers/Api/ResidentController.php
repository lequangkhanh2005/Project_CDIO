<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Resident::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string', 'unique:residents,phone'],
            'email' => ['nullable', 'email'],
            'building' => ['nullable', 'string'],
            'unit' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $resident = Resident::create($data);

        return response()->json([
            'message' => 'Đã tạo cư dân.',
            'data' => $resident,
        ], 201);
    }

    public function show(Resident $resident)
    {
        return response()->json([
            'data' => $resident,
        ]);
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'string', 'unique:residents,phone,' . $resident->id],
            'email' => ['nullable', 'email'],
            'building' => ['nullable', 'string'],
            'unit' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $resident->update($data);

        return response()->json([
            'message' => 'Đã cập nhật cư dân.',
            'data' => $resident,
        ]);
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();

        return response()->json([
            'message' => 'Đã xóa cư dân.',
        ]);
    }
}
