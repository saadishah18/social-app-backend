<?php

namespace Database\Seeders;

use App\Models\Plans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plans::create([
            'name' => 'Paid',
            'is_active' => '1',
        ]);
        Plans::create([
            'name' => 'Freemium',
            'is_active' => '1',
        ]);
    }
}
