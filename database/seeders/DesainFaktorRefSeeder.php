<?php

namespace Database\Seeders;

use App\Models\DesainFaktor;
use App\Models\DesainFaktorRef;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesainFaktorRefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Growth/Acquisition',
                'desain_faktor_id'=>DesainFaktor::where('nama','DF1')->first()->id,
            ],
            [
                'nama' => 'Innovation/Differentiation',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Cost Leadership',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Client Service/Stability',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF1')->first()->id,
            ],
            [
                'nama' => 'EG01—Portfolio of competitive products and services',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG02—Managed business risk',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG03—Compliance with external laws and regulations',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG05—Customer-oriented service culture',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG06—Business-service continuity and availability',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG07—Quality of management information',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG08—Optimization of internal business process functionality',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG09—Optimization of business process costs',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG10—Staff skills, motivation and productivity',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG11—Compliance with internal policies',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG12—Managed digital transformation programs',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG13—Product and business innovation',
                'desain_faktor_id' => DesainFaktor::where('nama', 'DF2')->first()->id,
            ],
        ];

        foreach ($data as $item) {
            DesainFaktorRef::create([
                'nama' => $item['nama'],
                'desain_faktor_id' => $item['desain_faktor_id'],
            ]);
        }
    }
}
