<?php

namespace Database\Seeders;

use App\Models\Assesment;
use App\Models\Organisasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$data = [
            [
                'nama' => 'MSI',
                'assesment_id'=>Assesment::where('nama',date('Y'))->first()->id
            ]
        ];

        foreach ($data as $item) {
            Organisasi::create([
                'nama' => $item['nama'],
                'assesment_id' => $item['assesment_id'],
            ]);
        }*/
    }
}
