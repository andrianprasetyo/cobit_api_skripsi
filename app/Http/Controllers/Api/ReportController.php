<?php

namespace App\Http\Controllers\Api;

use App\Exports\RespondenQuisionerHasilExport;
use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Quisioner\QuisionerHasilResource;
use App\Models\AssessmentUsers;
use App\Models\QuisionerHasil;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use JsonResponse;

    public function listJawabanResponden(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = QuisionerHasil::query();

        // if ($request->filled('assesment_id')) {
        //     $list->where('assesment_id', $request->assesment_id);
        // }
        if ($request->filled('search')) {
            $list->whereRelation('responden', 'nama', 'ilike', '%' . $search . '%');
            $list->orWhereRelation('responden','email', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page, QuisionerHasilResource::class);
        return $this->successResponse($data);
    }

    public function detailUserByID()
    {
        // $data = AssessmentUsers::with('assesmentquisionerhasil')->find($id);
        // if (!$data) {
        //     return $this->errorResponse('Data tidak ditemukan', 404);
        // }

        $list = QuisionerHasil::query()->get();
        $data = QuisionerHasilResource::collection($list);
        return $this->successResponse($data);
    }

    public function downloadExcel(Request $request)
    {
        $list = QuisionerHasil::query()->get()->toArray();
        $data= QuisionerHasilResource::collection($list);
        return Excel::download(new RespondenQuisionerHasilExport($list),'tes.xlsx');
    }
}
