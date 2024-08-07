<?php

namespace Database\Seeders;

use App\Models\Assesment;
use App\Models\Organisasi;
use Carbon\Carbon;
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
        $organisasi->nama="PT XYZ";
        $organisasi->deskripsi=null;
        $organisasi->save();

        $assesment=new Assesment();
        $assesment->nama="Assesment Cobit 2023";
        // $assesment->tahun='2023';
        $assesment->organisasi_id=$organisasi->id;
        $assesment->start_date=Carbon::now()->format('Y-m-d');
        $assesment->end_date=Carbon::now()->addMonth()->format('Y-m-d');
        $assesment->save();
    }
}
