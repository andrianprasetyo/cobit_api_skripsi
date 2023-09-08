<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssesmentCanvas;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use App\Models\Domain;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapabilityTargetLevelController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $target_id=$request->target_id;
        $list = CapabilityTargetLevel::with(['domain'])
            ->where('capability_target_id',$target_id);

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list);
        return $this->successResponse($data);
    }


    public function listDefaultTarget(Request $request)
    {
        // $sortBy = $request->get('sortBy', 'created_at');
        // $sortType = $request->get('sortType', 'desc');
        $assesment_id = $request->assesment_id;

        $ass = Assesment::find($assesment_id);

        $list=DB::table('assesment_canvas')
            ->join('domain','assesment_canvas.domain_id','=','domain_id')
            ->where('assesment_canvas.assesment_id',$assesment_id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $ass->minimum_target)
            ->select('domain.id','domain.kode','domain.ket','domain.urutan')
            ->groupBy('domain.id', 'domain.kode', 'domain.ket', 'domain.urutan')
            ->orderBy('domain.urutan','asc');

        $data = $this->paging($list);

        return $this->successResponse($data);
    }

    public function generateDomain(Request $request)
    {
        CobitHelper::generateTargetLevelDomain($request->id);
        // $domain=Domain::orderBy('urutan','asc')->get();
        // $target=[];
        // if(!$domain->isEmpty())
        // {
        //     foreach ($domain as $_item_domain) {

        //         $_exists=CapabilityTargetLevel::where('domain_id',$_item_domain->id)
        //             ->where('capability_target_id',$request->capability_target_id)
        //             ->exists();
        //         if(!$_exists)
        //         {
        //             // $_target=new CapabilityTargetLevel();
        //             // $_target->capability_target_id = $request->capability_target_id;
        //             // $_target->domain_id = $_item_domain->domain_id;
        //             // $_target->save();
        //             $target[]=array(
        //                 'capability_target_id'=>$request->capability_target_id,
        //                 'domain_id' => $_item_domain->id,
        //                 'created_at' => Carbon::now()->toDateTimeString(),
        //                 'updated_at' => Carbon::now()->toDateTimeString()
        //             );
        //         }
        //     }
        //     if(count($target) > 0)
        //     {
        //         CapabilityTargetLevel::insert($target);
        //     }
        // }

        return $this->successResponse();
    }

    public function saveUpdateTarget(Request $request)
    {
        $_target=$request->target;

        $id=$request->id;

        $cap_target=new CapabilityTarget();
        if($request->id != null)
        {
            $cap_target=CapabilityTarget::find($id);
        }
        $cap_target->nama=$request->nama;
        $cap_target->save();

        foreach ($_target as $_item_target) {
            if($_item_target['id'] != null)
            {
                $target=CapabilityTargetLevel::find($_item_target['id']);
            }
            if(!$target){
                $target = new CapabilityTargetLevel();
                $target->domain_id= $_item_target['domain_id'];
            }
            $target->capability_target_id = $cap_target->id;
            $target->target = $_item_target['target'];
            $target->save();
        }

        return $this->successResponse();
    }
}
