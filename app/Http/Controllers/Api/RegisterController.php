<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Models\User;
use App\Mail\SendWelcomeEmailToUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'role' => ["User"],
            'password' => Hash::make($request->password),
        ]);

        Mail::to($user->email)->send(new SendWelcomeEmailToUser($user));
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('messages.register'),
            'data' => new RegisterResource($user),
                'token' => $token
        ], 201);
    }
}
