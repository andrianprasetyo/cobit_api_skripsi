<?php

namespace Database\Seeders;

use App\Models\QuisionerGrupPilgan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuisionerGrupPilganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Grup A',
            ],
            [
                'nama' => 'Grup B',
            ]
        ];

        foreach ($data as $item) {
            QuisionerGrupPilgan::create([
                'nama' => $item['nama'],
            ]);
        }
    }
}
