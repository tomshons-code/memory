<?php

namespace App\Http\Resources\Course;

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
            'creator'   => new UserResource($this->creator),
            'count'     => $this->questions_count,
            'access'    => count($this->access),
        ];
    }
}
