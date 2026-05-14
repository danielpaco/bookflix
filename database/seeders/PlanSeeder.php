<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Mensual',
            'slug' => 'monthly',
            'price' => 9.99,
            'duration_days' => 30
        ]);

        Plan::create([
            'name' => 'Anual',
            'slug' => 'yearly',
            'price' => 99.99,
            'duration_days' => 365
        ]);
    }
}