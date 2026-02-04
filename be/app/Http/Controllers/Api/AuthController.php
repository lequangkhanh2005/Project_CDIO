<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'required_without:email'],
            'email' => ['nullable', 'email', 'required_without:phone'],
            'password' => ['required', 'string'],
        ]);

        $user = null;
        if (!empty($data['phone'])) {
            $user = User::where('phone', $data['phone'])->first();
        }
        if (!$user && !empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Sai thông tin đăng nhập.',
            ], 401);
        }

        $user->api_token = Str::random(60);
        $user->save();

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $user->api_token,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['nullable', 'in:admin,technician'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'technician',
            'api_token' => Str::random(60),
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $user->api_token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Đăng xuất thành công.',
        ]);
    }
}
