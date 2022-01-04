<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            return response(['status' => false, 'message' => __('Invalid email or password.')], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.login'),
            'data' => new AuthResource($user),
                'token' => $token
        ], 200);
    }

    public function logout()
    {

        auth()->user()->tokens()->delete();

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.logout'),
        ], 200);
    }
}
