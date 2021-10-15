<?php

namespace Database\Seeders;

use App\Helpers\getSystemInfoHelper;
use App\Helpers\Qs;
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
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '1',
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'First test',
                'term' => '2',
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '2',
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'First test',
                'term' => '3',
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],
            [
                'name' => 'Final test',
                'term' => '3',
                'year' => getSystemInfoHelper::getCurrentSession(),

            ],

        ];
        DB::table('exams')->insert($exams);
    }
}
