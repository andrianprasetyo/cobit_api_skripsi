<?php

namespace Database\Seeders;

use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
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
                'design_faktor_id'=>DesignFaktor::where('kode','DF1')->first()->id,
            ],
            [
                'nama' => 'Innovation/Differentiation',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Cost Leadership',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Client Service/Stability',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'EG01—Portfolio of competitive products and services',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG02—Managed business risk',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG03—Compliance with external laws and regulations',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG05—Customer-oriented service culture',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG06—Business-service continuity and availability',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG07—Quality of management information',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG08—Optimization of internal business process functionality',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG09—Optimization of business process costs',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG10—Staff skills, motivation and productivity',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG11—Compliance with internal policies',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG12—Managed digital transformation programs',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG13—Product and business innovation',
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
        ];

        foreach ($data as $item) {
            DesignFaktorKomponen::create([
                'nama' => $item['nama'],
                'design_faktor_id' => $item['design_faktor_id'],
            ]);
        }
    }
}
