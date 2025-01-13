<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::create([
            'user_id' => 1,
            'name' => 'Ferry Jean',
            'email' => 'jean.ferry@unistra.fr',
            'professor_id' => '8143',
        ]);
        Teacher::create([
            'user_id' => 2,
            'name' => 'Allegre Remi',
            'email' => 'remi.allegre@unistra.fr',
            'professor_id' => '8106',
        ]);
    }
}
