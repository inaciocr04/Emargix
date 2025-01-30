<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'name' => 'DW',
        ]);
        Course::create([
            'name' => 'SCN',
        ]);
        Course::create([
            'name' => 'CN',
        ]);
        Course::create([
            'name' => 'DW_A',
        ]);
        Course::create([
            'name' => 'SCN_A',
        ]);
        Course::create([
            'name' => 'CN_A',
        ]);
    }
}
