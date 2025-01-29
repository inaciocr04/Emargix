<?php

namespace Database\Seeders;

use App\Models\TpGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TpGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TpGroup::create([
            'name' => 'TP1',
        ]);
        TpGroup::create([
            'name' => 'TP2',
        ]);
        TpGroup::create([
            'name' => 'TP3',
        ]);
        TpGroup::create([
            'name' => 'TP4',
        ]);
        TpGroup::create([
            'name' => 'TP5',
        ]);
        TpGroup::create([
            'name' => 'TP6',
        ]);
    }
}
