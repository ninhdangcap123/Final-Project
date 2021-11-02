<?php
namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MyCoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('my_courses')->delete();
        $major = Major::pluck('id')->all();

        $data = [
            ['name' => 'Toán cao cấp', 'major_id' => $major[2]],
            ['name' => 'Cấu trúc dữ liệu và giải thuật', 'major_id' => $major[2]],
            ['name' => 'Vật liệu điện tử', 'major_id' => $major[2]],
            ['name' => 'Tính toán mô phỏng, đo lường', 'major_id' => $major[3]],
            ['name' => 'Toán học ứng dụng', 'major_id' => $major[3]],
            ['name' => 'Công nghệ tri thức và Máy học', 'major_id' => $major[4]],
            ['name' => 'Thị giác máy tính và Đa phương tiện', 'major_id' => $major[4]],
            ['name' => 'Toán chuyên ngành', 'major_id' => $major[5]],
            ['name' => 'Hệ thống cung cấp điện', 'major_id' => $major[5]],
            ['name' => 'Lập trình nhúng', 'major_id' => $major[5]],
        ];

        DB::table('my_courses')->insert($data);

    }
}
