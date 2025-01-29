<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(UserSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(TpGroupSeeder::class);
        $this->call(TDGroupSeeder::class);
        $this->call(TrainingSeeder::class);
    }
}
