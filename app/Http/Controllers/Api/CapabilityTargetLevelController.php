<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Capability\CapabilityTargetLevel\CapabilityTargetLevelResource;
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
        $target_id=$request->target_id;
        if($request->filled('assesment')){
            $list=DB::table('capability_target_level')
                ->join('capability_target','capability_target_level.capability_target_id','=','capability_target.id')
                ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
                ->whereNull('capability_target_level.deleted_at')
                ->whereNull('domain.deleted_at')
                // ->whereNotNull('capability_target_level.target')
                ->where('capability_target.assesment_id',$request->assesment_id)
                ->where('capability_target_level.capability_target_id', $request->target_id)
                ->select('capability_target_level.*')
                ->orderBy('domain.urutan','asc');

        }else{
            $list = DB::table('domain')
                ->leftJoin(DB::raw("capability_target_level ON domain.id=capability_target_level.domain_id AND capability_target_level.capability_target_id='$target_id'"), function ($join) {})
                ->whereNull('capability_target_level.deleted_at')
                ->whereNull('domain.deleted_at')
                ->select('capability_target_level.*','domain.id as id_domain')
                ->orderBy('domain.urutan', 'asc');
            $data = $this->paging($list, null, null);
        }

        $data = $this->paging($list, null, null, CapabilityTargetLevelResource::class);
        return $this->successResponse($data);
    }


    public function listDefaultTarget(Request $request)
    {
        // $sortBy = $request->get('sortBy', 'created_at');
        // $sortType = $request->get('sortType', 'desc');
        $assesment_id = $request->assesment_id;

        $ass = Assesment::find($assesment_id);
        if(!$ass)
        {
            return $this->errorResponse('Assesment tidak terdafatar',404);
        }

        $list=DB::table('assesment_canvas')
            ->join('domain','assesment_canvas.domain_id','=','domain.id')
            ->where('assesment_canvas.assesment_id',$ass->id)
            // ->where('assesment_canvas.aggreed_capability_level', '>=', $ass->minimum_target)
            ->whereNull('domain.deleted_at')
            ->select('domain.id','domain.kode','domain.ket','domain.urutan')
            // ->groupBy('domain.id', 'domain.kode', 'domain.ket', 'domain.urutan')
            ->orderBy('domain.urutan','asc');

        if ($request->filled('assesment')) {
            $list->where('assesment_canvas.aggreed_capability_level', '>=', $ass->minimum_target);
        }
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
        DB::beginTransaction();
        try {
            $_target = $request->target;

            $id = $request->id;
            $default=false;
            $cap_target = new CapabilityTarget();
            if ($request->filled('id')) {
                $cap_target = CapabilityTarget::find($id);
                if(!$cap_target)
                {
                    return $this->errorResponse('Target capability ID tidak terdaftar',404);
                }
                // $default=false;
            }
            $cap_target->nama = $request->nama;
            $cap_target->assesment_id = $request->assesment_id;
            $cap_target->default=$default;
            $cap_target->save();

            foreach ($_target as $_item_target) {
                if($_item_target['target'] != null){

                    $target = null;
                    if ($_item_target['id'] != null) {
                        $target = CapabilityTargetLevel::find($_item_target['id']);
                    }
                    if (!$target) {
                        $target = new CapabilityTargetLevel();
                        $target->domain_id = $_item_target['domain_id'];
                    }
                    $target->capability_target_id = $cap_target->id;
                    $target->target = $_item_target['target'];
                    $target->save();
                }else{
                    if($_item_target['id']){
                        // CapabilityTargetLevel::find($_item_target['id'])->delete();
                        CapabilityTargetLevel::where('id',$_item_target['id'])
                            ->update([
                                'target'=>null
                            ]);
                    }
                }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
}
