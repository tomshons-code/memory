<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Question\QuestionCollection;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $answer = $this->answer;
        return [
            'name'              => $this->name,
            'answer_points'     => 100,
            'correct_answers'   => $answer->correct_answers ?? 0,
            'bad_answers'       => $answer->bad_answers ?? 0,
            'level'             => $answer->level ?? 1,
            'points'            => $answer->points ?? 0,
            'time'              => $answer->time ?? 0,
            'finish'            => $answer->finish ?? 0,
            'images'            => collect(config('app-images'))->random(6),
            'questions'         => new QuestionCollection($this->whenLoaded('questions')),
        ];
    }
}
