<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin'])->except(['profile','updatePassword','update']);
    }

    public function profile(Request $request)
    {
        return new UserResource($request->user());
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

    public function index(Request $request)
    {
        $users = new User;
        $users = $users->with('roles')->role('author')->withCount("courses");
        if ($request->has('sort')) {
            $desc = $request->get('order') == 'desc' ? 'desc' : "ASC";
            if ($request->get('sort') == 'count') {
                $users->orderBy('courses_count', $desc);
            } else {
                $users->orderBy($request->get('sort'), $desc);
            }
        }
        $users = $users->orderBy('created_at', 'DESC')->paginate(5);
        return new UserCollection($users);
    }

    public function show(Request $request, User $user)
    {
        return new UserResource($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()], 403);
        }

        $user = new User;
        $user->name     = $request->input("name");
        $user->email    = $request->input("email");
        $user->password = Hash::make($request->input("password"));
        $user->save();
        $user->assignRole('author');

        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $input = $request->all();
        $authUser = $request->user();
        if ($authUser->hasRole('author') && $authUser->id != $user->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'string|max:255',
            'email' => [
                'string', 'email', 'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password'  => 'string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()], 403);
        }

        $user->password = Hash::make($request->input("password"));
        $user->update($request->only(["name", "email"]));
        $user->assignRole('author');

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return response()->json(["status" => "success", "message" => "Success! task deleted"], 200);
    }
}
