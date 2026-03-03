<?php

namespace App\Http\Resources\Ranking;

use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();
        return [
            'id' => $this->id,
            'points' => $this->answer->points,
            'nickname' => $this->nickname,
            'position' => $this->position,
            'my_result' => $this->id == $user->id ? 1 : 0
        ];
    }
}
