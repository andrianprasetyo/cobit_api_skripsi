<?php

namespace Database\Seeders;

use App\Models\QuisionerPilgan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuisionerPilganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'jawaban' => 'Kurang Penting',
            ],
            [
                'jawaban' => 'Agak Penting',
            ],
            [
                'jawaban' => 'Cukup Penting',
            ],
            [
                'jawaban' => 'Penting',
            ],
            [
                'jawaban' => 'Sangat Penting',
            ]
        ];

        $i=1;
        foreach ($data as $item) {
            QuisionerPilgan::create([
                'jawaban' => $item['jawaban'],
                'sorting'=>$i,
            ]);

            $i++;
        }
    }
}
