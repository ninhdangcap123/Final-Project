<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DormsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dorms')->delete();
        $data = [
            [ 'name' => 'KTX Co May' ],
            [ 'name' => 'KTX RMIT' ],
            [ 'name' => 'KTX Quoc gia' ],
            [ 'name' => 'KTX Lam Nghiep' ],
            [ 'name' => 'KTX FPT' ],
        ];
        DB::table('dorms')->insert($data);
    }
}
