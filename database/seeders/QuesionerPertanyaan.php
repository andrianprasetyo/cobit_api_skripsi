<?php

namespace Database\Seeders;

use App\Models\DesignFaktor;
use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerPertanyaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuesionerPertanyaan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data=[
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa penting masing-masing strategi perusahaan diatas yang seharusnya dapat dipenuhi di PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF1')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Penting')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa penting masing-masing tujuan tersebut untuk merealisasikan strategi PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF2')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Penting')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa besar dampak dari skenario risiko-risiko dibawah ini untuk PT Kereta Commuter Indonesia? *Level of Impact from Risks',
                'df'=> DesignFaktor::where('kode', 'DF3')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Kecil')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa besar kemungkinan dari risiko-risiko dibawah ini terjadi pada PT Kereta Commuter Indonesia? *Likelihood of Risks',
                'df'=> DesignFaktor::where('kode', 'DF3')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Mungkin')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa serius dampak dari isu-isu terkait IT ini jika terjadi pada PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF4')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Serius')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, saat ini PT Kereta Commuter Indonesia beroperasi di lingkungan dengan tingkat ancaman yang bagaimana?',
                'df'=> DesignFaktor::where('kode', 'DF5')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup High Normal')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, seberapa banyak persyaratan kepatuhan yang harus dipenuhi PT Kereta Commuter Indonesia saat ini?',
                'df'=> DesignFaktor::where('kode', 'DF6')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup High Normal Low')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, bagaimana seharusnya peran IT di PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF7')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Setuju')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, bagaimanakah seharusnya model pengadaan TI di PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF8')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup Outsource')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, bagaimanakah seharusnya metode implementasi IT yang dijalankan di PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF9')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup AgileDevOps')->first()->id
            ],
            [
                'pertanyaan'=>'Menurut Bapak/Ibu, bagaimanakah seharusnya strategi adopsi teknologi untuk PT Kereta Commuter Indonesia?',
                'df'=> DesignFaktor::where('kode', 'DF10')->first()->id,
                'jenis'=>QuisionerGrupJawaban::where('nama','Grup FirstMover')->first()->id
            ],
        ];
        foreach ($data as $key=>$item){
            $p=new QuisionerPertanyaan();
            $p->pertanyaan=$item['pertanyaan'];
            $p->design_faktor_id=$item['df'];
            $p->quisioner_grup_jawaban_id=$item['jenis'];
            $p->quisioner_id='81987685-8beb-4005-9ef3-9c74661552bf';
            $p->sorting=$key+1;
            $p->save();
        }
    }
}
