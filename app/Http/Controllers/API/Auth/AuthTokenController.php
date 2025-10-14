<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthTokenController extends Controller
{
    public function token(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token revoked']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles','permissions'));
    }
}
