<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CourseAdmin\CourseCollection;
use App\Http\Resources\CourseAdmin\CourseResource;
use App\Http\Resources\Question\QuestionCollection;
use Illuminate\Support\Str;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        $course = Course::with(['access','creator']);
        if ($user->hasRole('author')) {
            $course = $course->where('creator_id', '=', $user->id);
        }
        if ($request->has('sort')) {
            $desc = $request->get('order') == 'desc' ? 'desc' : "ASC";
            if ($request->get('sort') == 'count') {
                $course->orderBy('questions_count', $desc);
            } else {
                $course->orderBy($request->get('sort'), $desc);
            }
        }
        $course = $course->withCount("questions")->orderBy('created_at', 'DESC')->paginate(5);
        if ($course->count() > 0) {
            return new CourseCollection($course);
        }
        else {
            return response()->json(["status" => "no courses", "data" => []]);
        }
    }

    public function show(Request $request, Course $course)
    {
        $user = $request->user();
        if ($user->hasRole('author') && $user->id != $course->creator_id) {
            abort(404);
        }
        $course->loadCount("questions")->load('questions');
        return new CourseResource($course);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            "name"      => "required",
            "intro"     => "required",
            "desc"      => "required",
            "questions" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()], 403);
        }

        $course = new Course;
        $course->name       = $request->input("name");
        $course->teaser     = $request->input("intro");
        $course->content    = $request->input("desc");
        $course->img        = $request->input("src");
        $course->publish    = $request->input("publish");
        $course->password   = Str::random(10);
        $course->creator_id = $user->id;
        $course->save();

        if ($request->has("questions")) {
            collect($request->input("questions"))->each(function ($question, $key) use($course) {
                $course->questions()->create($question);
            });
            // $course->loadCount("questions")->load('questions');
        }

        return new CourseResource($course);
    }

    public function update(Request $request, Course $course)
    {
        $input = $request->all();
        $user = $request->user();
        if ($user->hasRole('author') && $user->id != $course->creator_id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            "name"      => "required",
            "intro"     => "required",
            "desc"      => "required",
            "questions" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()], 403);
        }

        $course->name       = $request->input("name");
        $course->teaser     = $request->input("intro");
        $course->content    = $request->input("desc");
        $course->img        = $request->input("src");
        $course->publish    = $request->input("publish");
        $course->save();

        if ($request->has("questions")) {
            collect($request->input("questions"))->each(function ($question, $key) use($course, $request) {
                if (isset($question['id'])) {
                    $course->questions()->where('id', $question['id'])->update($question);
                } else {
                    $course->questions()->create($question);
                }
            });
            // $course->loadCount("questions")->load('questions');
        }

        if ($request->has("delete_questions") && !empty($request->delete_questions)) {
            $course->questions()->whereIn('id', $request->delete_questions)->delete();
        }

        return new CourseResource($course);
    }

    public function destroy(Request $request, Course $course)
    {
        $user = $request->user();
        if ($user->hasRole('author') && $user->id != $course->creator_id) {
            abort(404);
        }

        $course->delete();
        return response()->json(["status" => "success", "message" => "Success! task deleted"], 200);
    }

    public function clone(Request $request, Course $course)
    {
        $user = $request->user();
        $newCourse              = $course->replicate();
        $newCourse->creator_id  = $user->id;
        $newCourse->password    = Str::random(10);
        $newCourse->save();
        $course->questions()->each(function ($question) use($newCourse) {
            $newCourse->questions()->create($question->toArray());
        });
        return new CourseResource($newCourse);
    }

    public function uploadimage(Request $request)
    {
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:100014',
                ]);
                $extension = $request->image->extension();
                $name = Str::random(25);
                $request->image->storeAs('/public', $name.".".$extension);
                $url = Storage::url($name.".".$extension);
                return response()->json(["status" => "success", "src" => $url, "url" => url($url)], 200);
            }
        }
        return response()->json(["status" => "No Image"], 404);
    }
}
