<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$data = [
            [
                'kode' => 'EDM01',
            ],
            [
                'kode' => 'EDM02',
            ],
            [
                'kode' => 'EDM03',
            ],
            [
                'kode' => 'EDM04',
            ],
            [
                'kode' => 'EDM05',
            ],
        ];*/
        $data=[
            [
                'kode'=>'EDM01'
            ],
            [
                'kode'=>'EDM02'
            ],
            [
                'kode'=>'EDM03'
            ],
            [
                'kode'=>'EDM04'
            ],
            [
                'kode'=>'EDM05'
            ],
            [
                'kode'=>'APO01'
            ],
            [
                'kode'=>'APO02'
            ],
            [
                'kode'=>'APO03'
            ],
            [
                'kode'=>'APO04'
            ],
            [
                'kode'=>'APO05'
            ],
            [
                'kode'=>'APO06'
            ],
            [
                'kode'=>'APO07'
            ],
            [
                'kode'=>'APO08'
            ],
            [
                'kode'=>'APO09'
            ],
            [
                'kode'=>'APO10'
            ],
            [
                'kode'=>'APO11'
            ],
            [
                'kode'=>'APO12'
            ],
            [
                'kode'=>'APO13'
            ],
            [
                'kode'=>'APO14'
            ],
            [
                'kode'=>'BAI01'
            ],
            [
                'kode'=>'BAI02'
            ],
            [
                'kode'=>'BAI03'
            ],
            [
                'kode'=>'BAI04'
            ],
            [
                'kode'=>'BAI05'
            ],
            [
                'kode'=>'BAI06'
            ],
            [
                'kode'=>'BAI07'
            ],
            [
                'kode'=>'BAI08'
            ],
            [
                'kode'=>'BAI09'
            ],
            [
                'kode'=>'BAI10'
            ],
            [
                'kode'=>'BAI11'
            ],
            [
                'kode'=>'DSS01'
            ],
            [
                'kode'=>'DSS02'
            ],
            [
                'kode'=>'DSS03'
            ],
            [
                'kode'=>'DSS04'
            ],
            [
                'kode'=>'DSS05'
            ],
            [
                'kode'=>'DSS06'
            ],
            [
                'kode'=>'MEA01'
            ],
            [
                'kode'=>'MEA02'
            ],
            [
                'kode'=>'MEA03'
            ],
            [
                'kode'=>'MEA04'
            ],
        ];

        foreach ($data as $key=>$item) {
            Domain::create([
                'kode' => $item['kode'],
                'urutan'=>$key
            ]);
        }
    }
}
