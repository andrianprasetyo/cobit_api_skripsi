<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CapabilityAssesment\CapabilityAssesmentLevelResource;
use App\Models\CapabilityAnswer;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentEvident;
use App\Models\CapabilityAssesmentSubmited;
use App\Models\CapabilityLevel;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CapabilityAssesmentController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $list=CapabilityLevel::with(['domain','capabilityass','capabilityass.capability_answer'])
            ->where('level',$request->level)
            ->where('domain_id', $request->domain_id)
            ->orderBy('urutan','asc');

        $_data_ass=$list->get();
        $data_ass=CapabilityAssesmentLevelResource::collection($_data_ass);
        $_total_bobot_level = [];
        $_total_bobot_answer=[];
        if(!$_data_ass->isEmpty())
        {
            foreach ($_data_ass as $_item_ass) {
                $_total_bobot_level[] = $_item_ass->bobot;
                if(isset($_item_ass->capabilityass->capability_answer->bobot))
                {
                    $_total_bobot_answer[]= (float)$_item_ass->capabilityass->capability_answer->bobot;
                }
            }
        }
        // $data = $this->paging($list, null, null, CapabilityAssesmentLevelResource::class);
        $data['list']= $data_ass;
        $data['answer']=CapabilityAnswer::orderBy('bobot','asc')->get();

        $total_bobot_answer = array_sum($_total_bobot_answer);
        $total_bobot_level=array_sum($_total_bobot_level);
        $total_result = $total_bobot_answer/ $total_bobot_level;
        $data['total_bobot']=array(
            'level'=>$total_bobot_level,
            'answer' => $total_bobot_answer,
            'result' => round($total_result,2)
        );

        $submited=CapabilityAssesmentSubmited::where('assesment_id',$request->assesment_id)
            ->where('domain_id',$request->domain_id)
            ->where('level',$request->level)
            ->where('submited',true)
            ->exists();

        $data['submited']=$submited;
        return $this->successResponse($data);
    }

    public function createAnswer(Request $request)
    {
        // dd($_POST);
        $request->validate(
            [
                'jawaban' => 'required|array',
            ],
            [
                'jawaban.required'=>'Jawaban harus di isi',
                'jawaban.array' => 'Jawaban harus dalam bentuk attay/list',
            ]
        );
        $jawaban = $request->jawaban;
        DB::beginTransaction();
        try {

            foreach ($jawaban as $_item_payload) {
                $capabilityass = $_item_payload['capabilityass'];

                $capability_ass = new CapabilityAssesment();
                if ($capabilityass['id'] != null) {
                    $capability_ass = CapabilityAssesment::find($capabilityass['id']);
                    if (!$capability_ass) {
                        return $this->errorResponse('Capbility asesment ID tidak ditemukan', 404);
                    }
                }
                $capability_ass->capability_level_id = $capabilityass['capability_level_id'];
                $capability_ass->capability_answer_id = $capabilityass['capability_answer_id'];
                $capability_ass->note = $capabilityass['note'];
                $capability_ass->ofi = $capabilityass['ofi'];
                $capability_ass->save();

                if(isset($_item_payload['evident']))
                {
                    $evident = $_item_payload['evident'];
                    if (count($evident) > 0) {
                        CapabilityAssesmentEvident::where('capability_assesment_id', $capability_ass->id)->delete();
                        $_evident = [];
                        foreach ($evident as $_item_evident) {
                            // $evident_doc=
                            $_evident[] = array(
                                'id'=>Str::uuid(),
                                'capability_assesment_id' => $capability_ass->id,
                                'url' => $_item_evident['url'],
                                'media_repositories_id' => $_item_evident['media_repositories_id'],
                            );
                        }
                        CapabilityAssesmentEvident::insert($_evident);
                    }
                }
            }

            CapabilityAssesmentSubmited::firstOrCreate([
                'assesment_id'=>$request->assesment_id,
                'domain_id' => $request->domain_id,
                'level' => $request->level,
                'submited'=>true
            ]);

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function uploadEvident(Request $request,$id)
    {
        $tipe=$request->tipe;
        $docs = $request->docs;
        $path = config('filesystems.path.evident');

        $payload=[];
        for ($r=0; $r < count($tipe); $r++) {
            $url = null;
            $files = null;
            if($tipe[$r] == 'file'){
                $_doc= $docs[$r];
                $filename = $_doc->hashName();
                $_doc->storeAs($path, $filename);
                $files = CobitHelper::Media($filename, $path, $_doc);
            }else{
                $url= $docs[$r];
            }
            $payload[]=array(
                'capability_assesment_id'=>$id,
                'tipe'=>$tipe[$r],
                'files'=>$files,
                'url' => $url,
            );

            $_new=new CapabilityAssesmentEvident();
            $_new->capability_assesment_id=$id;
            $_new->tipe= $tipe[$r];
            $_new->files=$files;
            $_new->url = $url;
            $_new->save();
        }

        return $this->successResponse($payload);
    }

    public function kalkukasiDomainByLevel(Request $request)
    {
        $list_level=CapabilityLevel::select('level')
            ->where('domain_id', $request->domain_id)
            ->groupBy('level')
            ->orderBy('level','ASC')
            ->get();


        $result = [];
        $total=0;
        if(!$list_level->isEmpty())
        {
            $_total=[];
            foreach ($list_level as $_item_level) {
                $_sum_bobot = DB::table('capability_assesment')
                    ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                    ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                    ->join('assesment_domain','capability_level.domain_id','=','assesment_domain.domain_id')
                    ->where('assesment_domain.assesment_id', $request->asesment_id)
                    ->where('capability_level.level', $_item_level->level)
                    ->where('capability_level.domain_id', $request->domain_id)
                    ->select(
                        // 'capability_assesment.id',
                        // 'capability_assesment.capability_level_id',
                        // 'capability_level.bobot as bobot_level',
                        'capability_answer.bobot as bobot_answer',
                    )
                    ->sum('capability_answer.bobot');

                $result[]=array(
                    'level'=>(float)$_item_level->level,
                    'kompilasi' => (float)$_sum_bobot,
                );
                $_total[]=(float)$_sum_bobot;
            }
            $total=round(array_sum($_total),2) + 1;
        }


        $data['list'] = $result;
        $data['total']=$total;
        return $this->successResponse($data);
    }
}
