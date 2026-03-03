<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Setting\SettingCollection;
use App\Http\Resources\Setting\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $settings = Setting::paginate(5);
        }

        if ($settings->count() > 0) {
            return new SettingCollection($settings);
        } else {
            return response()->json(["status" => "no settings", "data" => []]);
        }
    }

    public function show(Request $request, Setting $setting)
    {
        $user = $request->user();
        if ($user->hasRole('author')) {
            abort(404);
        }
        return new SettingResource($setting);
    }

    public function update(Request $request, Setting $setting)
    {
        $user = $request->user();
        if ($user->hasRole('author')) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            "desc"      => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()], 403);
        }
		$data = $request->only('name', 'desc');
        $setting->fill($data);
        $setting->save();

        return new SettingResource($setting);
    }

}
