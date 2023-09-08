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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $jawaban = $request->jawaban;
        DB::beginTransaction();
        try {

            $ev=[];
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
                        $ev=$_evident;
                        CapabilityAssesmentEvident::insert($_evident);
                    }
                }
            }

            DB::commit();
            return $this->successResponse($ev);
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
}
