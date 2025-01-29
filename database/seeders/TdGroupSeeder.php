<?php

namespace Database\Seeders;

use App\Models\TdGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TdGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TdGroup::create([
            'name' => 'TD1',
        ]);
        TdGroup::create([
            'name' => 'TD2',
        ]);
        TdGroup::create([
            'name' => 'TD3',
        ]);
        TdGroup::create([
            'name' => 'DW',
        ]);
        TdGroup::create([
            'name' => 'SCN',
        ]);
        TdGroup::create([
            'name' => 'CN',
        ]);
    }
}
