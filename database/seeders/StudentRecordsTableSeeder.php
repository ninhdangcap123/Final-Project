<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\StudentRecord;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createStudentRecord();
        $this->createManyStudentRecords(3);
    }

    protected function createStudentRecord()
    {
        $class = Classes::first();

        $user = User::factory()->create([
            'name' => 'Student Ninh',
            'user_type' => 'student',
            'username' => 'student',
            'password' => Hash::make('123'),
            'email' => 'student@student.com',

        ]);

        StudentRecord::factory()->create([
            'my_course_id' => $class->my_course_id,
            'user_id' => $user->id,
            'class_id' => $class->id
        ]);
    }

    protected function createManyStudentRecords(int $count)
    {
        $classes = Classes::all();

        foreach( $classes as $class ) {
            User::factory()
                ->has(
                    StudentRecord::factory()
                        ->state([
                            'class_id' => $class->id,
                            'my_course_id' => $class->my_course_id,
                            'user_id' => function (User $user) {
                                return [ 'user_id' => $user->id ];
                            },
                        ]), 'studentRecord')
                ->count($count)
                ->create([
                    'user_type' => 'student',
                    'password' => Hash::make('student'),
                ]);
        }

    }
}
