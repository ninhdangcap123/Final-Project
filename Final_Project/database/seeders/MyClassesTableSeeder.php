<?php
namespace Database\Seeders;

use App\Models\ClassType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MyClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('my_classes')->delete();
        $ct = ClassType::pluck('id')->all();

        $data = [
            ['name' => 'Toán cao cấp', 'class_type_id' => $ct[2]],
            ['name' => 'Cấu trúc dữ liệu và giải thuật', 'class_type_id' => $ct[2]],
            ['name' => 'Vật liệu điện tử', 'class_type_id' => $ct[2]],
            ['name' => 'Tính toán mô phỏng, đo lường', 'class_type_id' => $ct[3]],
            ['name' => 'Toán học ứng dụng', 'class_type_id' => $ct[3]],
            ['name' => 'Công nghệ tri thức và Máy học', 'class_type_id' => $ct[4]],
            ['name' => 'Thị giác máy tính và Đa phương tiện', 'class_type_id' => $ct[4]],
            ['name' => 'Toán chuyên ngành', 'class_type_id' => $ct[5]],
            ['name' => 'Hệ thống cung cấp điện', 'class_type_id' => $ct[5]],
            ['name' => 'Lập trình nhúng', 'class_type_id' => $ct[5]],
        ];

        DB::table('my_classes')->insert($data);

    }
}
