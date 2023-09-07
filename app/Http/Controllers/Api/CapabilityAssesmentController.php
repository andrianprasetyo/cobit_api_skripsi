<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CapabilityAssesment\CapabilityAssesmentResource;
use App\Models\CapabilityAnswer;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentEvident;
use App\Models\CapabilityLevel;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class CapabilityAssesmentController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $list=CapabilityLevel::with(['domain','capabilityass'])
            ->where('level',$request->level)
            ->where('domain_id', $request->domain_id)
            ->orderBy('urutan','asc');

        $data= $this->paging($list, null, null, CapabilityAssesmentResource::class);
        $data['answer']=CapabilityAnswer::orderBy('bobot','asc')->get();
        return $this->successResponse($data);
    }

    public function createAnswer(Request $request)
    {
        $jawaban=$request->jawaban;

        $tes=[];
        foreach ($jawaban as $_item_payload) {
            $capabilityass=$_item_payload['capabilityass'];

            $ass = new CapabilityAssesment();
            if($capabilityass['id'] != null){
                $ass=CapabilityAssesment::find($capabilityass['id']);
                if(!$ass)
                {
                    return $this->errorResponse('Capbility asesment ID tidak ditemukan',404);
                }
            }
            $ass->capability_level_id = $capabilityass['capability_level_id'];
            $ass->capability_answer_id = $capabilityass['capability_answer_id'];
            $ass->note = $capabilityass['note'];
            $ass->ofi = $capabilityass['ofi'];
            $ass->save();

            $tes[] = $capabilityass;
        }
        return $this->successResponse($tes);
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
}
