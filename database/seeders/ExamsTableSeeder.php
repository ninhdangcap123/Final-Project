<?php

namespace Database\Seeders;

use App\Helpers\GetSystemInfoHelper;
use DB;
use Illuminate\Database\Seeder;

class ExamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exams = [
            [
                'name' => 'First test',
                'term' => '1',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '1',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'First test',
                'term' => '2',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '2',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'First test',
                'term' => '3',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '3',
                'year' => GetSystemInfoHelper::getCurrentSession(),

            ],

        ];
        DB::table('exams')->insert($exams);
    }
}
