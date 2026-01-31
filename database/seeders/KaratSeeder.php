<?php

namespace Database\Seeders;

use App\Models\Karat;
use Illuminate\Database\Seeder;

class KaratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karats = [
            ['karat_value' => 24, 'multiplier' => 0.999, 'description' => '24k Gold'],
            ['karat_value' => 23, 'multiplier' => 0.965, 'description' => '23k Gold'],
            ['karat_value' => 22, 'multiplier' => 0.916, 'description' => '22k Gold'],
            ['karat_value' => 21, 'multiplier' => 0.875, 'description' => '21k Gold'],
            ['karat_value' => 18, 'multiplier' => 0.750, 'description' => '18k Gold'],
            ['karat_value' => 16, 'multiplier' => 0.666, 'description' => '16k Gold'],
            ['karat_value' => 14, 'multiplier' => 0.585, 'description' => '14k Gold'],
            ['karat_value' => 12, 'multiplier' => 0.500, 'description' => '12k Gold'],
            ['karat_value' => 10, 'multiplier' => 0.38, 'description' => '10k Gold'],
        ];

        foreach ($karats as $karat) {
            Karat::create($karat);
        }
    }
}
