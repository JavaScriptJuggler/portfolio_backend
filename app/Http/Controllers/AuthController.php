<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $response = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ])->save();
        return response()->json([
            'status' => $response,
            'message' => 'User Created Successful',
        ]);
    }
    /* login */
    public function login(Request $request)
    {
        try {
            if ($request->has('email') && $request->has('password')) {
                $credentials = request(['email', 'password']);

                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return $this->respondWithToken($token);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password are required',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                "mesasage" => $th->getMessage(),
            ]);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 60,
        ]);
    }
}
