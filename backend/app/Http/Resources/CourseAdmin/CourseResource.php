<?php

namespace App\Http\Resources\CourseAdmin;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Question\QuestionCollection;
use App\Http\Resources\User\UserResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'src'       => $this->img ? url($this->img) : null,
            'intro'     => $this->teaser,
            'desc'      => $this->content,
            'publish'   => (boolean)$this->publish,
            'count'     => $this->questions_count,
            'creator'   => new UserResource($this->creator),
            'password'  => $this->password,
            'questions' => new QuestionCollection($this->whenLoaded('questions')),
            'delete_questions'  => [],
        ];
    }
}
