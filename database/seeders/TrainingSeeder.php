<?php

namespace Database\Seeders;

use App\Models\Training;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Training::create([
            'name' => 'MMI1',
        ]);
        Training::create([
            'name' => 'MMI2',
        ]);
        Training::create([
            'name' => 'MMI3',
        ]);
        Training::create([
            'name' => 'CLIO1',
        ]);
        Training::create([
            'name' => 'CLIO2',
        ]);
        Training::create([
            'name' => 'CLIO3',
        ]);
        Training::create([
            'name' => 'GEII1',
        ]);
        Training::create([
            'name' => 'GEII2',
        ]);
        Training::create([
            'name' => 'GEII3',
        ]);
    }
}
