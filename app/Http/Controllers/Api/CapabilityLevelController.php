<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Capability\CapabilityAddRequest;
use App\Models\CapabilityLevel;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class CapabilityLevelController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = CapabilityLevel::with('domain');
        if ($request->filled('search')) {
            $list->where('kegiatan', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = CapabilityLevel::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
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

        // $data->kode = $request->kode;
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
