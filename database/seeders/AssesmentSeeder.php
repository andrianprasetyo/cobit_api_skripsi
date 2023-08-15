<?php

namespace Database\Seeders;

use App\Models\Assesment;
use App\Models\Organisasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssesmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$data = [
            [
                'nama' => date('Y'),
            ]
        ];

        foreach ($data as $item) {
            Assesment::create([
                'nama' => $item['nama']
            ]);
        }*/
        $organisasi=new Organisasi();
        $organisasi->nama="PT MSI";
        $organisasi->desksipsi=null;
        $organisasi->save();

        $assesment=new Assesment();
        $assesment->nama="Assesment Cobit 2023";
        $assesment->tahun='2023';
        $assesment->organisasi_id=$organisasi->id;
        $assesment->save();
    }
}
