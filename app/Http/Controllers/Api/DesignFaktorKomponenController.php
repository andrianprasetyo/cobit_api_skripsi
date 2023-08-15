<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktorKomponen;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DesignFaktorKomponenController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'nama');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $df_id = $request->design_faktor_id;

        $list = DesignFaktorKomponen::with('designfaktor');
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if($request->filled('design_faktor_id'))
        {
            $list->where('design_faktor_id',$df_id);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data = DesignFaktorKomponen::with('designfaktor')->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }
}
