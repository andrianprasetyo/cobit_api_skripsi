<?php

namespace Database\Seeders;

use App\Models\Assesment;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\AssessmentUsersHasil;
use App\Models\Organisasi;
use App\Models\Quisioner;
use App\Models\QuisionerHasil;
use App\Models\QuisionerPertanyaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyRespondentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Assesment::getQuery()->delete();
        AssessmentQuisioner::getQuery()->delete();
        AssessmentUsers::getQuery()->delete();
        AssessmentUsersHasil::getQuery()->delete();
        Organisasi::getQuery()->delete();
        QuisionerHasil::getQuery()->delete();

        $org=new Organisasi();
        $org->nama="Org-".rand(1,100);
        $org->save();

        $assesment=new Assesment();
        $assesment->nama='Assesment '.$org->nama.' '.rand(1,100);
        $assesment->tahun=date('Y');
        $assesment->organisasi_id=$org->id;
        $assesment->status='ongoing';
        $assesment->save();

        $quisionerId=Quisioner::where('aktif',true)->first()->id;
        $assesmentQues=new AssessmentQuisioner();
        $assesmentQues->assesment_id=$assesment->id;
        $assesmentQues->quisioner_id=$quisionerId;
        $assesmentQues->organisasi_id=$org->id;
        $assesmentQues->allow=true;
        $assesmentQues->save();

        for($i=1;$i<=3;$i++){
            $numbRand=rand(1,100);
            $respondent=new AssessmentUsers();
            $respondent->assesment_id=$assesment->id;
            $respondent->email=$numbRand.'@mail.com';
            $respondent->nama='Respondent '.$numbRand;
            $respondent->divisi="Divisi Hajat";
            $respondent->jabatan="Udag Idig";
            $respondent->code=Str::random(20);
            $respondent->status='done';
            $respondent->save();

            $pertanyaan=QuisionerPertanyaan::orderBy('sorting','ASC')->get();
            foreach($pertanyaan as $pert){
                $bobotArray=$pert->grup_jawaban->jawabans;
                $countBobot=count($bobotArray)-1;
                foreach($pert->design_faktor->design_faktor_komponen as $faktor){
                    $randIndex=rand(0,$countBobot);
                    $jawabanRandom=$bobotArray[$randIndex];

                    $hasil=new QuisionerHasil();
                    $hasil->quisioner_id=$quisionerId;
                    $hasil->quisioner_pertanyaan_id=$pert->id;
                    $hasil->jawaban_id=$jawabanRandom->id;
                    $hasil->assesment_users_id=$respondent->id;
                    $hasil->bobot=$jawabanRandom->bobot;
                    $hasil->save();
                }
            }
        }
    }
}
