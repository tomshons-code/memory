<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Answer;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => Client::inRandomOrder()->first()->id,
            'course_id' => Course::inRandomOrder()->first()->id,
        ];
    }
}
