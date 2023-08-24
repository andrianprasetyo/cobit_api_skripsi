<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentUsers;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class RespondenController extends Controller
{
    use JsonResponse;

    public function listJawabanResponden(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = AssessmentUsers::query();

        if($request->filled('assesment_id'))
        {
            $list->where('assesment_id',$request->assesment_id);
        }
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
            $list->orWhere('email', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailUserByID($id)
    {
        $data=AssessmentUsers::with('assesmentquisionerhasil')->find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        $this->successResponse($data);
    }
}
