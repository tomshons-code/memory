<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Answer\AnswerResource;
use App\Http\Resources\Question\QuestionCollection;

class AnswerController extends Controller
{

    public function update(Request $request, Course $answer)
    {
        $user = $request->user();
        $course = $answer;
        $course->load(['answer' => function($q) use ($user) {
            $q->where('client_id', $user->id);
        }]);
        $sum_points = $correct_answers = $bad_answers = 0;
        $send_answers = collect($request->get('answers'));
        if ($send_answers->count()) {
            $sum_points = $send_answers->sum('points');
            $correct_answers = $send_answers->where('correct', true)->count() ?? 0;
            $bad_answers = $send_answers->where('correct', false)->count() ?? 0;
            $map = $send_answers->mapWithKeys(function($item) {
                return [$item['id'] => [
                    'correct' => $item['correct']
                ]];
            });
            $user->course_question()->syncwithoutdetaching($map);
        }
        $user_questions = $user->course_question()->where('course_id', $course->id)->pluck('question_id')->toArray();
        $questions = Question::where('course_id', $course->id)->whereNotIn('id', $user_questions)->inRandomOrder()->take(2)->get();

        if (empty($course->answer)) { // rozpoczęcie kursu
            $course->answer()->create([
                'client_id' => $user->id,
                'finish'    => 0,
            ]);
        } elseif ($send_answers->count()) { // jeżeli są wysłane odpowiedzi
            $update = $course->answer()->where('client_id', $user->id);
            $update->update([
                'points'    => $course->answer->points + $sum_points,
                'finish'    => $questions->isEmpty() ? 1 : 0, // jeżeli brak kolejnych pytań zakończ kurs
                'time'      => $request->get('time') ?? 0,
                'correct_answers'   => $course->answer->correct_answers + $correct_answers,
                'bad_answers'       => $course->answer->bad_answers + $bad_answers,
            ]);
            $update->increment('level');
        } elseif ($request->has('finish')) {
            $update = $course->answer()->where('client_id', $user->id);
            if ($course->answer->finish && $request->get('finish') == false) { // jeżeli rozpoczęte od nowa
                $course->answer()->update([
                    'level'     => 1,
                    'points'    => 0,
                    'finish'    => 0,
                    'time'      => 0,
                    'correct_answers'   => 0,
                    'bad_answers'       => 0,
                ]);
                $user->course_question()->detach($course->questions->pluck('id')->toArray());
                $course = Course::where('slug', $course->slug)->first();
                $user_questions = $user->course_question()->where('course_id', $course->id)->pluck('question_id')->toArray();
                $questions = Question::where('course_id', $course->id)->whereNotIn('id', $user_questions)->inRandomOrder()->take(2)->get();
            } elseif ($request->get('finish') == true) {  // jeżeli zakończone ręcznie
                $update->update([
                    'finish'    => 1,
                    'time'      => $request->get('time') ?? 0,
                ]);
            }
        }
        $course->load(['answer' => function($q) use ($user) {
            $q->where('client_id', $user->id);
        }]);
        return (new AnswerResource($course))->additional(['data' => [
            'questions' => new QuestionCollection($questions),
        ]]);

    }

}
