<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function store(LoginRequest $request) {
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json
        ([
            'token' => $token,
            'user' => JWTAuth::user()
        ], 200);
    }

    public function destroy(Request $request)
    {
        // Logout dengan JWT (untuk menghapus token di server)
        JWTAuth::invalidate(JWTAuth::getToken());
        
        return response()->json(['message' => 'Successfully logged out']);
    }
}
