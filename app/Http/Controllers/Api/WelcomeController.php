<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentUsers;
use App\Models\AssessmentUsersHasil;
use App\Models\DesignFaktor;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Traits\JsonResponse;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    use JsonResponse;

    public function halo()
    {
        return $this->successResponse();
    }
    public function prosesHasilQuisioner(){
        ini_set('max_execution_time', 300);
        $users=AssessmentUsers::where('is_proses',null)->get();
        foreach($users as $user){
            $u=AssessmentUsers::where('id',$user->id)->first();
            $u->is_proses='running';
            $u->save();
            $this->getQuisionerHasil($user->id);
            $u->is_proses='done';
            $u->save();
        }
    }
    private function getQuisionerHasil($assesment_user_id){
        $designFaktor=DesignFaktor::get();
        AssessmentUsersHasil::where('assesment_user_id',$assesment_user_id)->delete();
        foreach($designFaktor as $df){
            $straightProsesDF=['DF1','DF4','DF5','DF6','DF7','DF8','DF9','DF10'];
            if(in_array($df->kode,$straightProsesDF)){
                $this->prosesHasilStraight($assesment_user_id,$df->id);
            }
        }
    }
    private function prosesHasilStraight($assesment_user_id,$designFaktorId){
        // get data kuesioner respondent by df
        $dataQuesioner=DB::select("
            SELECT
                qj.bobot as jawaban_bobot,
                dfk.baseline,
                dfk.design_faktor_id,
                dfk.nama,
                dfk.deskripsi,
                qj.jawaban,
                qh.*

            FROM
                quisioner_hasil qh
                JOIN quisioner_pertanyaan qp ON qh.quisioner_pertanyaan_id = qp.ID
                JOIN design_faktor_komponen dfk ON dfk.id=qh.design_faktor_komponen_id
                JOIN quisioner_jawaban qj ON qj.id=qh.jawaban_id
            WHERE
                qh.assesment_users_id = :assesment_user_id
                AND qp.design_faktor_id = :design_faktor_id
            ORDER BY dfk.urutan asc
        ",[
            'assesment_user_id'=>$assesment_user_id,
            'design_faktor_id'=>$designFaktorId
        ]);

        //mapping nilai importance dan baseline
        $importance=[];
        $baseLine=[];
        foreach($dataQuesioner as $val){
            $importance[]=$val->jawaban_bobot;
            $baseLine[]=$val->baseline;
        }

        $domain=Domain::get();
        foreach($domain as $dom){
            // get nilai map berdasarkan df dan domain
            $map=DB::select("
                SELECT
                    *
                FROM
                    design_faktor_map dfm
                    JOIN design_faktor_komponen dfk ON dfk.ID = dfm.design_faktor_komponen_id
                WHERE
                    dfm.design_faktor_id = :design_faktor_id
                    AND dfm.domain_id = :domain_id
                ORDER BY
                    dfk.urutan ASC
            ",[
                'design_faktor_id'=>$designFaktorId,
                'domain_id'=>$dom->id
            ]);
            $mapValue=[];
            foreach($map as $val){
                $mapValue[]=$val->nilai;
            }

            // perkalian matrik score
            $hasilScore=0;
            $hasilBaseLine=0;
            foreach($importance as $key=>$imp){
                $hasilScore+=$mapValue[$key]*$imp;
                $hasilBaseLine+=$mapValue[$key]*$baseLine[$key];
            }

            // pencarian nilai relative importance
            //1. rata-rata importance score
            $totalData=count($importance);
            $totalImportance=0;
            foreach($importance as $imp){
                $totalImportance+=$imp;
            }
            $avgImportance=$totalImportance/$totalData;

            //2. rata-rata baseline / rata-rata importance
            $totalBaseline=0;
            foreach($baseLine as $bs){
                $totalBaseline+=$bs;
            }
            $avgBaseLine=($totalBaseline/$totalData)/$avgImportance;

            //3. mencari relative importance
            $nilaiAwal=$avgBaseLine*100*$hasilScore/$hasilBaseLine;
            $nilaiAwal=5 * round($nilaiAwal / 5); // pembulatan kelipatan 5
            $relativeImportance=$nilaiAwal-100;

            /*$cekExistData=AssessmentUsersHasil::where('assesment_user_id',$assesment_user_id)->where('domain_id',$dom->id)->first();
            if(!$cekExistData){

            }else{
                $save=$cekExistData;
            }*/
            $save=new AssessmentUsersHasil();
            $save->design_faktor_id=$designFaktorId;
            $save->assesment_user_id=$assesment_user_id;
            $save->domain_id=$dom->id;
            $save->score=$hasilScore;
            $save->baseline_score=$hasilBaseLine;
            $save->relative_importance=$relativeImportance;
            $save->save();
        }
        echo "Done";
    }
}
