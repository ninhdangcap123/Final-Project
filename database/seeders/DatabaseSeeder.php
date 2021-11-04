<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BloodGroupsTableSeeder::class);
        $this->call(GradesTableSeeder::class);
        $this->call(DormsTableSeeder::class);
        $this->call(MajorsTableSeeder::class);
        $this->call(UserTypesTableSeeder::class);
        $this->call(MyCoursesTableSeeder::class);
        $this->call(NationalitiesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(LgasTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SubjectsTableSeeder::class);
        $this->call(ClassesTableSeeder::class);
        $this->call(StudentRecordsTableSeeder::class);
        $this->call(SkillsTableSeeder::class);
        $this->call(ExamsTableSeeder::class);
    }
}
