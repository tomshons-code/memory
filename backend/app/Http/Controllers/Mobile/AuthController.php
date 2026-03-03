<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginMobileRequest;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Setting\SettingResource;
use App\Models\Client;
use App\Models\Setting;

class AuthController extends Controller
{

    public function profile(Request $request)
    {
        return new ClientResource($request->user());
    }

    public function about(Request $request)
    {
        $lang = request()->input('lang');
        if ($lang == 'pl')
            $id = 2;
        if ($lang == 'en')
            $id = 4;
        
        $settings = Setting::where('id', $id)->first();
        return new SettingResource($settings);
    }

    public function profileSave(Request $request)
    {
        $user = $request->user();
        $data = $this->validate($request, [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'nickname'     => 'required',
        ]);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->nickname = $data['nickname'];
        $user->save();
        return [
            'message' => 'Updated'
        ];
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed|string|min:6',
        ]);

        if (!$user || !Hash::check($data['old_password'], $user->password)) {
            return response()->json(['message' => 'Bad old password'], 405);
        }

        $user->password = Hash::make($data['password']);
        $user->save();
        $user->tokens()->delete(); #KASUJ ZALOGOWANYCH
        return [
            'message' => 'Updated'
        ];
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'nickname'      => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:clients',
            'password'      => 'required|confirmed|string|min:6',
        ]);

        $user = Client::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'nickname'      => $request->nickname,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(LoginMobileRequest $request)
    {
        $user = $request->authenticate();
        // $request->user()->tokens()->delete(); KASUJ ZALOGOWANYCH

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
