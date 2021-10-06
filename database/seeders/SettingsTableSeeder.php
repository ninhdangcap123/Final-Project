<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();

        $data = [
            ['type' => 'current_session', 'description' => '2021-2022'],
            ['type' => 'system_title', 'description' => 'TGMA'],
            ['type' => 'system_name', 'description' => 'TGMA ACADEMY'],
            ['type' => 'term_ends', 'description' => '7/10/2022'],
            ['type' => 'term_begins', 'description' => '7/10/2021'],
            ['type' => 'phone', 'description' => '0123456789'],
            ['type' => 'address', 'description' => 'Hanoi'],
            ['type' => 'system_email', 'description' => 'tgmaacademy@tgma.com'],
            ['type' => 'alt_email', 'description' => ''],
            ['type' => 'email_host', 'description' => ''],
            ['type' => 'email_pass', 'description' => ''],
            ['type' => 'lock_exam', 'description' => 0],
            ['type' => 'logo', 'description' => ''],
            ['type' => 'next_term_fees_cn1', 'description' => '20000'],
            ['type' => 'next_term_fees_cn2', 'description' => '25000'],
            ['type' => 'next_term_fees_cn3', 'description' => '25000'],
            ['type' => 'next_term_fees_cn4', 'description' => '25600'],
            ['type' => 'next_term_fees_cn8', 'description' => '15600'],
            ['type' => 'next_term_fees_cn11', 'description' => '1600'],
        ];

        DB::table('settings')->insert($data);

    }
}
