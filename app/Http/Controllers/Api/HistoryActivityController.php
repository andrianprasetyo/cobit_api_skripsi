<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistoryActivity;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class HistoryActivityController extends Controller
{
    use JsonResponse;
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        if (!$request->filled('limit')) {
            $limit = null;
            $page = null;
        }
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = HistoryActivity::orderBy($sortBy, $sortType);
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data = HistoryActivity::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        return $this->successResponse($data);
    }
}
