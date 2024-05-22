<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Capability\CapabilityAddRequest;
use App\Http\Resources\CapabilityLevel\CapabilityLevelResource;
use App\Models\CapabilityLevel;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapabilityLevelController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'domain.urutan');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $domain_id = $request->domain_id;
        $level = $request->level;

        // $list = CapabilityLevel::with('domain');
        $list=DB::table('capability_level')
            ->join('domain','capability_level.domain_id','=','domain.id')
            ->whereNull('capability_level.deleted_at')
            ->select('capability_level.*');


        if ($request->filled('search')) {
            $list->where(function ($query) use ($search) {
                $query->where('capability_level.kode', 'ilike', '%' . $search . '%')
                      ->orWhere('capability_level.kegiatan', 'ilike', '%' . $search . '%');
            });

            $list->where('capability_level.kode', 'ilike', '%' . $search . '%')->whereNull('capability_level.deleted_at');
            $list->orWhere('capability_level.kegiatan', 'ilike', '%' . $search . '%')->whereNull('capability_level.deleted_at');
        }
        if ($request->filled('domain_id')){
            $list->where(function ($query) use ($domain_id) {
                $query->where('capability_level.domain_id',$domain_id);
            });
        }
        if ($request->filled('level')) {
             $list->where(function ($query) use ($level) {
                $query->where('capability_level.level',$level);
            });
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page, CapabilityLevelResource::class);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = CapabilityLevel::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse(new CapabilityLevelResource($data));
    }

    public function add(CapabilityAddRequest $request)
    {
        $request->validated();

        $data = new CapabilityLevel();
        $data->kode = $request->kode;
        $data->kegiatan = $request->kegiatan;
        $data->translate = $request->translate;
        $data->level = $request->level;
        $data->bobot = $request->bobot;
        $data->domain_id = $request->domain_id;
        $data->save();

        return $this->successResponse();
    }

    public function edit(Request $request,$id)
    {
        $data = CapabilityLevel::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        $data->kode = $request->kode;
        $data->kegiatan = $request->kegiatan;
        $data->translate = $request->translate;
        $data->level = $request->level;
        $data->bobot = $request->bobot;
        $data->urutan = $request->urutan;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = CapabilityLevel::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }

}
