<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Course extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = ['name', 'slug', 'teaser', 'content', 'publish', 'password', 'img', 'creator_id'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function access()
    {
        return $this->belongsToMany(Client::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }

    public function scopePublish($query)
    {
        return $query->where('publish', true);
    }
}
