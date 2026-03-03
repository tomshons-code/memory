<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginAdminRequest;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{

    public function login(LoginAdminRequest $request)
    {
        $user = $request->authenticate();
        $user->load('roles');
        // $request->user()->tokens()->delete(); KASUJ ZALOGOWANYCH

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'typ'           => isset($user->roles) ? $user->roles->first()->id : null
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
