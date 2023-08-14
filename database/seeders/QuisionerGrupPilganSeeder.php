<?php

namespace Database\Seeders;

use App\Models\QuisionerGrupJawaban;
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
                'nama' => 'Grup Pilgan',
                'jenis'=>'pilgan',
            ],
            [
                'nama' => 'Grup Persentase',
                'jenis'=>'persentase'
            ]
        ];

        foreach ($data as $item) {
            QuisionerGrupJawaban::create([
                'nama' => $item['nama'],
                'jenis' => $item['jenis'],
            ]);
        }
    }
}
