<?php

namespace Database\Seeders;

use App\Models\CapabilityAnswer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CapabilityAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Not Value',
                'label' => 'N/A',
                'bobot' => 0,
            ],
            [
                'nama' => 'Not Achived',
                'label' => 'N',
                'bobot' => 0,15,
            ],
            [
                'nama' => 'Partially',
                'label' => 'P',
                'bobot' => 0.50,
            ],
            [
                'nama' => 'Largelly',
                'label' => 'L',
                'bobot' => 0.85,
            ],
            [
                'nama' => 'Fully',
                'label' => 'F',
                'bobot' => 1,
            ],
        ];

        foreach ($data as $item) {
            CapabilityAnswer::create([
                'nama' => $item['nama'],
                'label' => $item['label'],
                'bobot' => $item['bobot'],
            ]);
        }
    }
}
