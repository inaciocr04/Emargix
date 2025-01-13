<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::create([
            'lastname' => 'Rodrigues',
            'firstname' => 'Inacio',
            'email' => 'inacio.rodrigues@etu.unistra.fr',
        ]);

        Student::create([
            'lastname' => 'Rodrigues',
            'firstname' => 'Xavière',
            'email' => 'xaviere.rodrigues@etu.unistra.fr',
        ]);
    }
}
