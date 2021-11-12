<?php

namespace Database\Factories;

use App\Helpers\GetSystemInfoHelper;
use App\Models\Classes;
use App\Models\MyCourse;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'session' => GetSystemInfoHelper::getCurrentSession(),
            'my_course_id' => MyCourse::first()->id,
            'class_id' => Classes::first()->id,
            'user_id' => null
        ];
    }
}
