<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_types')->delete();

        $data = [
            ['name' => 'Công Nghệ Thông Tin', 'code' => 'CN1'],
            ['name' => 'Kỹ Thuật Máy Tính', 'code' => 'CN2'],
            ['name' => 'Vật Lý Kỹ Thuật', 'code' => 'CN3'],
            ['name' => 'Cơ Kỹ Thuật', 'code' => 'CN4'],
            ['name' => 'Khoa Học Máy Tính', 'code' => 'CN8'],
            ['name' => 'Tự Động Hóa', 'code' => 'CN11'],
        ];

        DB::table('class_types')->insert($data);

    }
}
