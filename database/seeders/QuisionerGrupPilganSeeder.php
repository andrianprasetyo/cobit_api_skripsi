<?php

namespace Database\Seeders;

use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerJawaban;
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
                'nama' => 'Grup Penting',
                'jenis'=>'pilgan',
                'jawaban'=>[
                    [
                        'jawaban'=>'Kurang Penting',
                        'bobot'=>1
                    ],
                    [
                        'jawaban'=>'Agak Penting',
                        'bobot'=>2
                    ],
                    [
                        'jawaban'=>'Cukup Penting',
                        'bobot'=>3
                    ],
                    [
                        'jawaban'=>'Penting',
                        'bobot'=>4
                    ],
                    [
                        'jawaban'=>'Sangat Penting',
                        'bobot'=>5
                    ],
                ]
            ],
            [
                'nama' => 'Grup Kecil',
                'jenis'=>'pilgan',
                'jawaban'=>[
                    [
                        'jawaban'=>'Sangat Kecil',
                        'bobot'=>1
                    ],
                    [
                        'jawaban'=>'Kecil',
                        'bobot'=>2
                    ],
                    [
                        'jawaban'=>'Sedang',
                        'bobot'=>3
                    ],
                    [
                        'jawaban'=>'Besar',
                        'bobot'=>4
                    ],
                    [
                        'jawaban'=>'Sangat Besar',
                        'bobot'=>5
                    ],
                ]
            ],
            [
                'nama' => 'Grup Mungkin',
                'jenis'=>'pilgan',
                'jawaban'=>[
                    [
                        'jawaban'=>'Sangat Tidak Mungkin',
                        'bobot'=>1
                    ],
                    [
                        'jawaban'=>'Tidak Mungkin',
                        'bobot'=>2
                    ],
                    [
                        'jawaban'=>'Mungkin',
                        'bobot'=>3
                    ],
                    [
                        'jawaban'=>'Sangat Mungkin',
                        'bobot'=>4
                    ],
                    [
                        'jawaban'=>'Hampir Pasti',
                        'bobot'=>5
                    ],
                ]
            ],
            [
                'nama' => 'Grup Serius',
                'jenis'=>'pilgan',
                'jawaban'=>[
                    [
                        'jawaban'=>'Tidak Serius',
                        'bobot'=>1
                    ],
                    [
                        'jawaban'=>'Serius',
                        'bobot'=>2
                    ],
                    [
                        'jawaban'=>'Sangat Serius',
                        'bobot'=>3
                    ],
                ]
            ],
            [
                'nama' => 'Grup High Normal',
                'jenis'=>'persentase',
                'jawaban'=>[
                    [
                        'jawaban'=>'Persentase HN',
                        'bobot'=>100
                    ]
                ]
            ],
            [
                'nama' => 'Grup Setuju',
                'jenis'=>'pilgan',
                'jawaban'=>[
                    [
                        'jawaban'=>'Sangat Tidak Setuju',
                        'bobot'=>1
                    ],
                    [
                        'jawaban'=>'Tidak Setuju',
                        'bobot'=>2
                    ],
                    [
                        'jawaban'=>'Setuju',
                        'bobot'=>3
                    ],
                    [
                        'jawaban'=>'Sangat Setuju',
                        'bobot'=>4
                    ]
                ]
            ],
            [
                'nama' => 'Grup High Normal Low',
                'jenis'=>'persentase',
                'jawaban'=>[
                    [
                        'jawaban'=>'Persentase HNL',
                        'bobot'=>100
                    ]
                ]
            ],
            [
                'nama' => 'Grup Outsource',
                'jenis'=>'persentase',
                'jawaban'=>[
                    [
                        'jawaban'=>'Persentase Outsource',
                        'bobot'=>100
                    ]
                ]
            ],
            [
                'nama' => 'Grup AgileDevOps',
                'jenis'=>'persentase',
                'jawaban'=>[
                    [
                        'jawaban'=>'Persentase Agile',
                        'bobot'=>100
                    ]
                ]
            ],
            [
                'nama' => 'Grup FirstMover',
                'jenis'=>'persentase',
                'jawaban'=>[
                    [
                        'jawaban'=>'persentase Firstmover',
                        'bobot'=>100
                    ]
                ]
            ]
        ];

        foreach ($data as $item) {
            /*QuisionerGrupJawaban::create([
                'nama' => $item['nama'],
                'jenis' => $item['jenis'],
            ]);*/
            $qg=new QuisionerGrupJawaban();
            $qg->nama=$item['nama'];
            $qg->jenis=$item['jenis'];
            $qg->save();

            foreach($item['jawaban'] as $key=>$j){
                $qj=new QuisionerJawaban();
                $qj->jawaban=$j['jawaban'];
                $qj->quisioner_grup_jawaban_id=$qg->id;
                $qj->bobot=$j['bobot'];
                $qj->sorting=$key+1;
                $qj->save();
            }
        }
    }
}
