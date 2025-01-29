<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '1',
            'name' => 'Jean Ferry',
            'email' => 'jean.ferry@unistra.fr',
            'password' => Hash::make('password'),
            'role' => 'teacher'
        ]);

        User::create([
            'id' => '2',
            'name' => 'Rémi allègre',
            'email' => 'remi.allegre@unistra.fr',
            'password' => Hash::make('password'),
            'role' => 'teacher'
        ]);

    }
}
