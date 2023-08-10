<?php

namespace Database\Seeders;

use App\Models\DesainFaktor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesainFaktorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <= 10; $i++) {
            DesainFaktor::create([
                'nama'=>'DF'.$i
            ]);
        }
    }
}
