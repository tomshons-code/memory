<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'question', 'answers', 'correct', 'answer_description'];
    protected $casts = [
        'answers' => 'object',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function client_question()
    {
        return $this->belongsToMany(Client::class);
    }
}
