<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DesignFaktorController extends Controller
{
    use JsonResponse;
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'kode');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;

        $list=DesignFaktor::query();
        if ($request->filled('search'))
        {
            $list->where('kode', 'ilike', '%' . $search . '%');
            $list->orWhere('nama', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data=DesignFaktor::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        return $this->successResponse($data);
    }
}
