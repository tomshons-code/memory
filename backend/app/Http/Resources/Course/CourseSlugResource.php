<?php

namespace App\Http\Resources\Course;

use App\Http\Resources\Setting\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSlugResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = request()->input('lang');
        if ($lang == 'pl')
            $id = 1;
        if ($lang == 'en')
            $id = 3;
        $settings = Setting::where('id', $id)->first();
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'desc'      => $this->content,
            'count'     => $this->questions_count,
            'rules'     => new SettingResource($settings),
            'points'    => isset($this->answer) ? $this->answer->points : 0,
            'finish'    => isset($this->answer) ? $this->answer->finish : null,
        ];
    }
}
