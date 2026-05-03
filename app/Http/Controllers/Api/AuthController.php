<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'business_name' => 'required|string',
            'nib' => 'required|unique:members',
            'phone' => 'required'
        ]);

        $user = $this->authService->registerMember($validated);
        return response()->json(['message' => 'Registrasi Berhasil', 'user' => $user->load('member')], 201);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login Gagal'], 401);
        }

        $user = User::find(Auth::id())->load('member');
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}
