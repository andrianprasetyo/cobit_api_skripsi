<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CapabilityTargetLevel;
use App\Models\Domain;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function generateDomain(Request $request)
    {
        $domain=Domain::orderBy('urutan','asc')->get();
        $target=[];
        if(!$domain->isEmpty())
        {
            foreach ($domain as $_item_domain) {

                $_exists=CapabilityTargetLevel::where('domain_id',$_item_domain->id)
                    ->where('capability_target_id',$request->capability_target_id)
                    ->exists();
                if(!$_exists)
                {
                    // $_target=new CapabilityTargetLevel();
                    // $_target->capability_target_id = $request->capability_target_id;
                    // $_target->domain_id = $_item_domain->domain_id;
                    // $_target->save();
                    $target[]=array(
                        'capability_target_id'=>$request->capability_target_id,
                        'domain_id' => $_item_domain->id,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()
                    );
                }
            }
            if(count($target) > 0)
            {
                CapabilityTargetLevel::insert($target);
            }
        }

        return $this->successResponse();
    }

    public function saveUpdateTarget(Request $request)
    {
        $target=$request->target;

        foreach ($target as $_item_target) {
            $_target=CapabilityTargetLevel::find($_item_target['id']);
            if($_target)
            {
                $_target->target = $_item_target['target'];
                $_target->save();
            }
        }

        return $this->successResponse();
    }
}
