<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssesmentDomain;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapabilityTargetController extends Controller
{
    use JsonResponse;
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;
        $assesment_id = $request->assesment_id;

        $list = CapabilityTarget::with('assesment');
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }
        if($request->filled('assesment_id'))
        {
            $list->where('assesment_id',$assesment_id);
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByAssesment($id)
    {
        $data = CapabilityTarget::where('assesment_id',$id)->get();
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = CapabilityTarget::with(['capabilitytargetlevel'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'nama'=>'required',
                'assesment_id' => 'required|exists:assesment,id',
                'target' => 'required|array',
            ],
            [
                'nama.required'=>'Target harus di isi',
                'assesment_id.required' => 'Assesment harus di isi',
                'assesment_id.exists' => 'Assesment tidak terdaftar',
                'target.required' => 'Target harus di isi',
                'target.array' => 'Target harus dalam bentuk array/list',
            ]
        );
        $assesment_id = $request->assesment_id;
        $listtarget=$request->target;

        $_check_target = CapabilityTarget::where('assesment_id', $assesment_id)
            ->where('nama', $request->nama)
            ->first();

        if($_check_target)
        {
            return $this->errorResponse('Nama target sudah digunakan',400);
        }

        DB::beginTransaction();
        try {
            $target = new CapabilityTarget();
            $target->nama = $request->nama;
            $target->assesment_id = $request->assesment_id;
            $target->default = false;
            $target->save();

            $ass_domain = [];
            $target_level = [];

            foreach ($listtarget as $_item_target) {
                $ass_domain[] = array(
                    'domain_id' => $_item_target['domain_id'],
                    'assesment_id' => $assesment_id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                );

                $target_level[] = array(
                    'domain_id' => $_item_target['domain_id'],
                    'target' => $_item_target['target'],
                    'capability_target_id' => $target->id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                );
            }

            AssesmentDomain::insert($ass_domain);
            CapabilityTargetLevel::insert($target_level);

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function edit(Request $request,$id)
    {
        $data = CapabilityTarget::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak tersedia', 404);
        }
        $data->nama = $request->nama;
        $data->assesment_id = $request->assesment_id;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = CapabilityTarget::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak tersedia',404);
        }
        $data->delete();

        return $this->successResponse();
    }
}
