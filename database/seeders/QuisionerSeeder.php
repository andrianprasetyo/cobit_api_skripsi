<?php

namespace Database\Seeders;

use App\Models\Quisioner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuisionerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'pertanyaan' => 'Menurut Bapak/Ibu, seberapa penting masing-masing strategi perusahaan diatas yang seharusnya dapat',
            ],
            [
                'pertanyaan' => 'Berdasarkan framework COBIT 2019, terdapat 13 tujuan perusahaan yang dapat digunakan untuk merealisasikan strategi perusahaan, yaitu: ',
            ],
            [
                'pertanyaan' => 'Menurut Bapak/Ibu, seberapa penting masing-masing tujuan tersebut untuk merealisasikan strategi PT Kereta Commuter Indonesia?',
            ],
            [
                'pertanyaan' => 'Menurut Bapak/Ibu, seberapa besar dampak dari skenario risiko-risiko dibawah ini untuk PT Kereta Commuter Indonesia? *Level of Impact from Risks',
            ],
            [
                'pertanyaan' => 'Dampak risiko terkait pengelolaan siklus proyek dan program (misalnya: pembengkakan anggaran proyek TI, keterlambatan proyek TI dan sebagainya)',
            ],
            [
                'pertanyaan' => 'Dampak risiko terkait beban, biaya dan pengawasan TI (misalnya: kekurangan anggaran investasi TI, persyaratan yang tidak memadai, kegagalan SLA, atau kelebihan biaya dalam proses pengadaan TI, dan sebagainya)',
            ],
            [
                'pertanyaan' => 'Dampak risiko terkait keahlian TI, skill TI, dan perilaku karyawan (contoh: ketergantungan terhadap karyawan tertentu, kekurangan pelatihan TI, ketidakmampuan untuk merekrut atau mempertahankan talenta digital, dan sebagainya)',
            ],
            [
                'pertanyaan' => 'Dampak risiko terkait Enterprise/IT architecture (contoh: inefisiensi atau duplikasi aplikasi TI karena tidak adanya arsitektur TI perusahaan, kegagalan dalam mengadopsi dan mengeksploitasi teknologi baru, kegagalan dalam menyingkirkan aplikasi yang telah obsolete dan sebagainya)',
            ]
        ];

        $i=1;
        foreach ($data as $item) {
            Quisioner::create([
                'pertanyaan' => $item['pertanyaan'],
                'sorting'=>$i,
            ]);
            $i++;
        }
    }
}
