<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'fullname' => $data['fullname'] ?? null,
            'role' => User::ROLE_USER,
        ]);

        return response()->json(['success' => true], 201);
    }

    public function auth(AuthRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])
            ->where('role', User::ROLE_USER)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid data',
                'errors' => [
                    'email' => ['Invalid credentials'],
                ],
            ], 422);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }
}
