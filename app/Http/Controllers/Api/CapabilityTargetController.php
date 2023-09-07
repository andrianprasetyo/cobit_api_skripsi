<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CapabilityTarget;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

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
        $data=new CapabilityTarget();
        $data->nama=$request->nama;
        $data->assesment_id = $request->assesment_id;
        $data->save();

        return $this->successResponse();
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
