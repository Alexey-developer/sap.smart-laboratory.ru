<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\LoginRequest;
use App\Models\Api\v1\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Laravel\Passport\Passport;

class PassportAuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Авторизационные данные некорректны!'], 422);
            }
        } else {
            $user = User::create([
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password']),
            ]);
        }

        Passport::personalAccessTokensExpireIn(now()->addSeconds(86400));
        $token = $user->createToken('authToken');
        $access_token = $token->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $access_token
        ]);
    }
}
