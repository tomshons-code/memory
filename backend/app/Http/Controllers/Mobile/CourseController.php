<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\Course\CourseCollection;
use App\Http\Resources\Ranking\RankingCollection;
use App\Http\Resources\Ranking\RankingResource;
use App\Http\Resources\Course\CourseSlugResource;
use Illuminate\Database\Eloquent\Builder;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $name = request()->input('name');
        $courses = Course::with(['access' => function($q) use ($user) {
            $q->where('client_id', $user->id);
        }, 'creator']);
        if ($name) {
            $courses->where('name', 'like', '%'.$name.'%');
        }
        $courses = $courses->withCount(["questions"])->Publish()->paginate(5);
        return new CourseCollection($courses);
    }

    public function ranking(Request $request, Course $course)
    {
        $user = $request->user();
        $users = Client::whereHas('answer', function (Builder $q) use ($course)  {
            $q->where('course_id', $course->id);
        })->with('answer');
        $users = $users->get()->sortByDesc(function($i, $key) {
            return $i->answer->points;
        })->flatten()->map(function ($item, $index) {
            $item->position = $index + 1;
            return $item;
        });
        $user = $users->firstWhere('id', $user->id);

        return (new RankingCollection($users->paginate(20)))->additional([
            'my_result' => isset($user) ? new RankingResource($user) : (object)[],
            'name' => $course->name
        ]);
    }

    public function show(Request $request, Course $course)
    {
        $user = $request->user();
        if (!$user->course_access()->where('course_id', '=', $course->id)->count())
            return response()->json(["status" => "no access"], 404);

        $course->load(['answer' => function($q) use ($user) {
            $q->where('client_id', $user->id);
        }]);
        // $client_answer = $user->client_answer($course->id)->get();
        $course->loadCount("questions");
        return response()->json(["status" => "success", "data" => new CourseSlugResource($course)], 200);
    }

    public function access(Course $course, Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            "password"   => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "no access", "validation_errors" => $validator->errors()], 405);
        }

        if ($request->password != $course->password) {
            return response()->json(["status" => "no access"], 405);
        }

        $user->course_access()->syncWithoutDetaching($course);
        return response()->json(["status" => "success"]);
    }

}
