<?php
namespace App\Helpers;

use App\Models\AssessmentUsers;
use App\Models\AssessmentUsersHasil;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorMapAdditional;
use App\Models\Domain;
use Illuminate\Support\Facades\DB;

class CobitHelper
{
    public static function Media($filename, $path, $file)
    {
        $mime = array(
            'filename' => $filename,
            'path' => $path . $filename,
            // 'url'=>url('/'.$path.$filename),
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
            'originalname' => $file->getClientOriginalName(),
            'ext' => $file->extension()
        );
        return $mime;
    }
    public static function getQuisionerHasil($assesment_user_id){
        $checkQuesionerDone=AssessmentUsers::where('status','done')->where('id',$assesment_user_id)->first();
        if($checkQuesionerDone){
            $designFaktor=DesignFaktor::get();
            AssessmentUsersHasil::where('assesment_user_id',$assesment_user_id)->delete();
            $u=AssessmentUsers::where('id',$assesment_user_id)->first();
            $u->is_proses='running';
            $u->save();
            foreach($designFaktor as $df){
                $straightProsesDF=['DF1','DF3','DF4','DF5','DF6','DF7','DF8','DF9','DF10'];
                if(in_array($df->kode,$straightProsesDF)){
                    CobitHelper::prosesHasilStraight($assesment_user_id,$df->id);
                }else if($df->kode=='DF2'){
                    CobitHelper::prosesHasilDF2($assesment_user_id,$df->id);
                }
            }
            $u->is_proses='done';
            $u->save();
        }
    }

    public static function prosesHasilDF2($assesment_user_id,$designFaktorId){
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
        //handle for question>1
        $dataQuesioner2=[];
        foreach($dataQuesioner as $dq){
            //check eksis value
            $designFactorComponentValue=$dq->design_faktor_komponen_id;
            $filtered_array = array_filter($dataQuesioner2, function ($obj) use ($designFactorComponentValue) {
                return $obj->design_faktor_komponen_id == $designFactorComponentValue;
            });

            if(count($filtered_array)==1){
                foreach($filtered_array as $key=>$value){
                    $jawaban_bobot=$value->jawaban_bobot;
                    $jawaban_bobot*=$dq->jawaban_bobot;
                    $dataQuesioner2[$key]=(object)[
                        'jawaban_bobot'=>$jawaban_bobot,
                        'baseline'=>$value->baseline,
                        'nama'=>$value->nama,
                        'deskripsi'=>$value->deskripsi,
                        'jawaban'=>$value->jawaban,
                        'id'=>$value->id,
                        'quisioner_id'=>$value->quisioner_id,
                        'quisioner_pertanyaan_id'=>$value->quisioner_pertanyaan_id,
                        'jawaban_id'=>$value->jawaban_id,
                        'assesment_users_id'=>$value->assesment_users_id,
                        'bobot'=>$value->bobot,
                        'design_faktor_komponen_id'=>$value->design_faktor_komponen_id
                    ];
                }
            }else{
                $dataQuesioner2[]=(object)[
                    'jawaban_bobot'=>$dq->jawaban_bobot,
                    'baseline'=>$dq->baseline,
                    'nama'=>$dq->nama,
                    'deskripsi'=>$dq->deskripsi,
                    'jawaban'=>$dq->jawaban,
                    'id'=>$dq->id,
                    'quisioner_id'=>$dq->quisioner_id,
                    'quisioner_pertanyaan_id'=>$dq->quisioner_pertanyaan_id,
                    'jawaban_id'=>$dq->jawaban_id,
                    'assesment_users_id'=>$dq->assesment_users_id,
                    'bobot'=>$dq->bobot,
                    'design_faktor_komponen_id'=>$dq->design_faktor_komponen_id
                ];
            }
        }
        $dataQuesioner=$dataQuesioner2;
        //END handle for question>1

        //mapping nilai importance dan baseline
        $importance=[];
        $pureImportance=[];
        $baseLine=[];
        $pureBaseLine=[];
        foreach($dataQuesioner as $val){
            $importance[]=$val->jawaban_bobot;
            $baseLine[]=$val->baseline;
        }
        $pureImportance=$importance;
        $pureBaseLine=$baseLine;

        $importance2=[];
        $baseLine2=[];

        // Matrix 1
        $getDomainMapAdditional=DesignFaktorMapAdditional::orderBy('urutan','ASC')->get();
        foreach($getDomainMapAdditional as $key=>$value){
            $map=DB::select("
                SELECT
                    *
                FROM
                    design_faktor_map dfm
                    JOIN design_faktor_map_additional dfk ON dfk.ID = dfm.design_faktor_map_additional_id
                WHERE
                    dfm.design_faktor_id = :design_faktor_id
                    AND dfm.design_faktor_map_additional_id = :domain_id
                ORDER BY
                    dfk.urutan ASC
            ",[
                'design_faktor_id'=>$designFaktorId,
                'domain_id'=>$value->id
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
            $importance2[]=$hasilScore;
            $baseLine2[]=$hasilBaseLine;
        }

        $importance=$importance2;
        $baseLine=$baseLine2;
        // End Matrix 1

        //Matrix 2

        $result=[];
        $domain=Domain::orderBy('urutan','ASC')->get();
        foreach($domain as $dom){

            // get nilai map berdasarkan df dan domain
            $map=DB::select("
                    SELECT
                        *
                    FROM
                        design_faktor_map dfm
                        JOIN design_faktor_map_additional dfk ON dfk.ID = dfm.design_faktor_map_additional_id
                    WHERE
                        dfm.design_faktor_id = :design_faktor_id
                        AND dfm.domain_id=:domain_id
                    ORDER BY
                        dfk.urutan ASC
                ",[
                'design_faktor_id'=>$designFaktorId,
                'domain_id'=>$dom->id,
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
//            dd($hasilBaseLine);
            // pencarian nilai relative importance
            //1. rata-rata importance score
            $totalData=count($pureImportance);
            $totalImportance=0;
            foreach($pureImportance as $imp){
                $totalImportance+=$imp;
            }
            // $avgImportance=$totalImportance / $totalData;
            $avgImportance = $totalData !=0?$totalImportance/$totalData:0;

            //2. rata-rata baseline / rata-rata importance
            $totalBaseline=0;
            foreach($pureBaseLine as $bs){
                $totalBaseline+=$bs;
            }
            // $avgBaseLine = ($totalBaseline / $totalData) / $avgImportance;
            $avgBaseLine= $avgImportance !=0?($totalBaseline/$totalData)/$avgImportance:0;

            //3. mencari relative importance
            // $nilaiAwal=$avgBaseLine*100*$hasilScore/$hasilBaseLine;
            $nilaiAwal = $hasilBaseLine !=0?$avgBaseLine * 100 * $hasilScore / $hasilBaseLine:0;
            $nilaiAwal=5 * round($nilaiAwal / 5); // pembulatan kelipatan 5
            $relativeImportance=$nilaiAwal-100;

            /*$cekExistData=AssessmentUsersHasil::where('assesment_user_id',$assesment_user_id)->where('domain_id',$dom->id)->first();
            if(!$cekExistData){

            }else{
                $save=$cekExistData;
            }*/

            $result []=array(
                'design_faktor_id'=> $designFaktorId,
                'assesment_user_id'=> $assesment_user_id,
                'domain_id'=> $dom->id,
                'score'=> $hasilScore,
                'baseline_score'=> $hasilBaseLine,
                'relative_importance'=> $relativeImportance,
            );

            // $save=new AssessmentUsersHasil();
            // $save->design_faktor_id=$designFaktorId;
            // $save->assesment_user_id=$assesment_user_id;
            // $save->domain_id=$dom->id;
            // $save->score=$hasilScore;
            // $save->baseline_score=$hasilBaseLine;
            // $save->relative_importance=$relativeImportance;
            // $save->save();
        }

        AssessmentUsersHasil::create($result);
        return true;
    }
    public static function prosesHasilStraight($assesment_user_id,$designFaktorId){
        // get data kuesioner respondent by df
        $dataQuesioner=DB::select("
            SELECT
                qh.bobot as jawaban_bobot,
                -- qj.bobot as jawaban_bobot,
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

        //handle for question>1
        $dataQuesioner2=[];
        foreach($dataQuesioner as $dq){
            //check eksis value
            $designFactorComponentValue=$dq->design_faktor_komponen_id;
            $filtered_array = array_filter($dataQuesioner2, function ($obj) use ($designFactorComponentValue) {
                return $obj->design_faktor_komponen_id == $designFactorComponentValue;
            });

            if(count($filtered_array)==1){
                foreach($filtered_array as $key=>$value){
                    $jawaban_bobot=$value->jawaban_bobot;
                    $jawaban_bobot*=$dq->jawaban_bobot;
                    $dataQuesioner2[$key]=(object)[
                        'jawaban_bobot'=>$jawaban_bobot,
                        'baseline'=>$value->baseline,
                        'nama'=>$value->nama,
                        'deskripsi'=>$value->deskripsi,
                        'jawaban'=>$value->jawaban,
                        'id'=>$value->id,
                        'quisioner_id'=>$value->quisioner_id,
                        'quisioner_pertanyaan_id'=>$value->quisioner_pertanyaan_id,
                        'jawaban_id'=>$value->jawaban_id,
                        'assesment_users_id'=>$value->assesment_users_id,
                        'bobot'=>$value->bobot,
                        'design_faktor_komponen_id'=>$value->design_faktor_komponen_id
                    ];
                }
            }else{
                $dataQuesioner2[]=(object)[
                    'jawaban_bobot'=>$dq->jawaban_bobot,
                    'baseline'=>$dq->baseline,
                    'nama'=>$dq->nama,
                    'deskripsi'=>$dq->deskripsi,
                    'jawaban'=>$dq->jawaban,
                    'id'=>$dq->id,
                    'quisioner_id'=>$dq->quisioner_id,
                    'quisioner_pertanyaan_id'=>$dq->quisioner_pertanyaan_id,
                    'jawaban_id'=>$dq->jawaban_id,
                    'assesment_users_id'=>$dq->assesment_users_id,
                    'bobot'=>$dq->bobot,
                    'design_faktor_komponen_id'=>$dq->design_faktor_komponen_id
                ];
            }
        }
        $dataQuesioner=$dataQuesioner2;
        //END handle for question>1

        //mapping nilai importance dan baseline
        $importance=[];
        $baseLine=[];
        foreach($dataQuesioner as $val){
            $importance[]=$val->jawaban_bobot;
            $baseLine[]=$val->baseline;
        }

        $result=[];
        $domain=Domain::orderBy('urutan','ASC')->get();
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
            // $avgImportance=$totalImportance / $totalData;
            $avgImportance = $totalData !=0?$totalImportance/$totalData:0;

            //2. rata-rata baseline / rata-rata importance
            $totalBaseline=0;
            foreach($baseLine as $bs){
                $totalBaseline+=$bs;
            }
            // $avgBaseLine = ($totalBaseline / $totalData) / $avgImportance;
            $avgBaseLine= $avgImportance !=0?($totalBaseline/$totalData)/$avgImportance:0;


            //3. mencari relative importance
            $nilaiAwal=$hasilBaseLine !=0?$avgBaseLine*100*$hasilScore/$hasilBaseLine:0;
            $nilaiAwal=5 * round($nilaiAwal / 5); // pembulatan kelipatan 5
            $relativeImportance=$nilaiAwal-100;

            /*$cekExistData=AssessmentUsersHasil::where('assesment_user_id',$assesment_user_id)->where('domain_id',$dom->id)->first();
            if(!$cekExistData){

            }else{
                $save=$cekExistData;
            }*/


            // $save=new AssessmentUsersHasil();
            // $save->design_faktor_id=$designFaktorId;
            // $save->assesment_user_id=$assesment_user_id;
            // $save->domain_id=$dom->id;
            // $save->score=$hasilScore;
            // $save->baseline_score=$hasilBaseLine;
            // $save->relative_importance=$relativeImportance;
            // $save->save();

            $result[]=array(
                'design_faktor_id'=> $designFaktorId,
                'assesment_user_id'=> $assesment_user_id,
                'domain_id'=> $dom->id,
                'score'=> $hasilScore,
                'baseline_score'=> $hasilBaseLine,
                'relative_importance' => $relativeImportance,
            );
        }

        AssessmentUsersHasil::create($result);
        return true;
    }
}
