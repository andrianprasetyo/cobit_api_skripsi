<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CapabilityAssesment\CapabilityAssesmentResource;
use App\Models\CapabilityAnswer;
use App\Models\CapabilityAssesment;
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
        $data['answer']=CapabilityAnswer::orderBy('nama','asc')->get();
        return $this->successResponse($data);
    }

    public function createAnswer(Request $request)
    {
        $jawaban=$request->jawaban;
        foreach ($jawaban as $_item_payload) {
            $capabilityass=$_item_payload['capabilityass'];

            $ass = new CapabilityAssesment();
            if($capabilityass['id'] != null){
                $ass=CapabilityAssesment::find($capabilityass['id']);
            }
            $ass->capability_level_id = $capabilityass['capability_level_id'];
            $ass->capability_answer_id = $capabilityass['capability_answer_id'];
            $ass->note = $capabilityass['note'];
            $ass->ofi = $capabilityass['ofi'];
            $ass->save();
        }
        return $this->successResponse();
    }
}
