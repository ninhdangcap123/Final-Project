<?php
namespace Database\Seeders;

use App\Models\MyCourse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes')->delete();
        $c = MyCourse::pluck('id')->all();

        $data = [
            ['name' => 'A', 'my_course_id' => $c[0], 'active' => 1],
            ['name' => 'B', 'my_course_id' => $c[0], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[1], 'active' => 1],
            ['name' => 'B', 'my_course_id' => $c[1], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[2], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[3], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[4], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[5], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[6], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[7], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[8], 'active' => 1],
            ['name' => 'A', 'my_course_id' => $c[9], 'active' => 1],
        ];

        DB::table('classes')->insert($data);
    }
}
