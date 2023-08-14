<?php

namespace Database\Seeders;

use App\Models\QuisionerJawaban;
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
            QuisionerJawaban::create([
                'jawaban' => $item['jawaban'],
                'sorting'=>$i,
            ]);

            $i++;
        }
    }
}
