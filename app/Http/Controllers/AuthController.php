<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credintials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken("API Token of " . $user->name)->plainTextToken
        ], "User Login Successfully.");

    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // return response()->json("This is my register method");
        return $this->success([
            'user' => $user,
            'token' => $user->createToken("API Token of " . $user->name)->plainTextToken,
        ], "User created successfully.");
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message' => 'You have successfully been logged out and your token has been deleted.'
        ]);
    }
}
