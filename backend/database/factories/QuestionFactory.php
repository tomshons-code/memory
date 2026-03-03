<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id' => Course::inRandomOrder()->first()->id,
            'question' => $this->faker->name,
            'correct' => $this->faker->randomElement([0,1,2]),
            'answer_description'   => $this->faker->word,
            'answers' => $this->faker->sentences(3)
        ];
    }
}
