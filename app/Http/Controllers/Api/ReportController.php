<?php

namespace App\Http\Controllers\Api;

use App\Exports\RespondenQuisionerHasilExport;
use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Quisioner\QuisionerHasilResource;
use App\Models\AssesmentDesignFaktorWeight;
use App\Models\AssessmentUsers;
use App\Models\DesignFaktor;
use App\Models\Domain;
use App\Models\QuisionerHasil;
use App\Models\QuisionerPertanyaan;
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

        $list = QuisionerHasil::with(['pertanyaan'])->get();
        $data = QuisionerHasilResource::collection($list);
        return $this->successResponse($list);
    }

    public function downloadExcel(Request $request)
    {
        $pertanyaan = QuisionerPertanyaan::all();
        $list = QuisionerHasil::query()->get();
        // $data= QuisionerHasilResource::collection($list);
        $data['pertanyaan']=$pertanyaan;
        return Excel::download(new RespondenQuisionerHasilExport($data),'tes.xlsx');
    }

    public function canvas(Request $request)
    {

        $hasil=Domain::with([
            'assesmenthasil'=>function($q) use ($request){
                $q->where('assesment_id',$request->assesment_id);
            },
            'assesmenthasil.designfaktor',
            'assesmentcanvas'=>function($q) use($request){
                $q->where('assesment_id', $request->assesment_id);
            },
        ])
        ->orderBy('urutan','ASC')
        ->get();

        $weight=AssesmentDesignFaktorWeight::with(['designfaktor'])
            ->get();

        $df=DesignFaktor::orderBy('urutan','ASC')
        ->get();


        $data['hasil']=$hasil;
        $data['weight'] = $weight;
        $data['df'] = $df;
        return $this->successResponse($data);
    }

}
