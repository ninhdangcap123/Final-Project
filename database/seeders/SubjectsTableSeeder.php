<?php

namespace Database\Seeders;

use App\Models\MyCourse;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subjects')->delete();

        $this->createSubjects();
    }

    protected function createSubjects()
    {
        $subjects = [ 'Giới Thiệu Môn', 'Chuyên Ngành', 'Kết Thúc Môn' ];
        $sub_slug = [ 'GT', 'CN', 'KT' ];
        $teacher_id = User::where([ 'user_type' => 'teacher' ])->first()->id;
        $my_courses = MyCourse::all();

        foreach( $my_courses as $my_course ) {

            $data = [

                [
                    'name' => $subjects[0],
                    'slug' => $sub_slug[0],
                    'my_course_id' => $my_course->id,
                    'teacher_id' => $teacher_id
                ],

                [
                    'name' => $subjects[1],
                    'slug' => $sub_slug[1],
                    'my_course_id' => $my_course->id,
                    'teacher_id' => $teacher_id
                ],
                [
                    'name' => $subjects[2],
                    'slug' => $sub_slug[2],
                    'my_course_id' => $my_course->id,
                    'teacher_id' => $teacher_id
                ],

            ];

            DB::table('subjects')->insert($data);
        }

    }

}
