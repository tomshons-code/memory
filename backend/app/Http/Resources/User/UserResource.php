<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'count' => $this->when(isset($this->courses_count), $this->courses_count),
            'typ'   => $this->whenLoaded('roles', function () {
                return $this->roles->first()->id;
            }),
        ];
    }
}
