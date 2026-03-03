<?php

namespace App\Http\Resources\Question;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (is_null($this->resource)) {
            return [];
        }
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer_description' => $this->answer_description,
            'answers' => $this->answers,
            'correct' => $this->correct,
        ];
    }
}
