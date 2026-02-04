<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->select(['id', 'name', 'email', 'phone', 'role']);
        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        return response()->json([
            'data' => $query->orderBy('name')->get(),
        ]);
    }
}
