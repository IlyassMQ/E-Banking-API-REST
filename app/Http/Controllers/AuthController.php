<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Account created successfully',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $reqValData = $request->validated();

        if (!$token = Auth::attempt($reqValData)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Login successful',
            'access_token' => $token,
            'user' => Auth::user()
        ], 200);
    }

    public function me()
    {
        return response()->json([
            'user' => Auth::user()
        ]);
    }
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => 'Success',
            'message' => 'Logout successful'
        ],200);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'Success',
            'access_token' => Auth::refresh(),
        ]);
    }
}