<?php

namespace App\Http\Controllers\Api;

use App\Exports\RespondenQuisionerHasilExport;
use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Canvas\AdjustmenValueCanvasRequest;
use App\Http\Requests\Canvas\AdjustmenWeightValueRequest;
use App\Http\Resources\Quisioner\QuisionerHasilResource;
use App\Http\Resources\Report\AssesmentDesignFaktorWeightCanvasResource;
use App\Http\Resources\Report\DesignFaktorCanvasResource;
use App\Http\Resources\Report\DomainCanvasResource;
use App\Jobs\SetCanvasHasilDataJob;
use App\Models\Assesment;
use App\Models\AssesmentCanvas;
use App\Models\AssesmentDesignFaktorWeight;
use App\Models\AssesmentHasil;
use App\Models\AssessmentUsers;
use App\Models\DesignFaktor;
use App\Models\Domain;
use App\Models\QuisionerHasil;
use App\Models\QuisionerPertanyaan;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $list = QuisionerHasil::with(['responden']);

        if ($request->filled('assesment_id')) {
            $list->whereRelation('responden','assesment_id', $request->assesment_id);
        }

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
        try {
            $pertanyaan = QuisionerPertanyaan::orderBy('sorting', 'ASC')->get();
            // return $this->successResponse($pertanyaan);
            // $list = QuisionerHasil::query()->get();
            // // $data= QuisionerHasilResource::collection($list);
            // $data['pertanyaan']=$pertanyaan;
            return Excel::download(new RespondenQuisionerHasilExport($pertanyaan), 'tes.xlsx');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    //
    public function setHasilCanvas($id)
    {
        $_assesment = Assesment::where('id', $id)->exists();
        if (!$_assesment) {
            return $this->errorResponse('Assesment tidak terdaftar', 404);
        }
        DB::beginTransaction();
        try {
            // ini_set('max_execution_time', 300);
            // // $assesment = Assesment::get();
            // CobitHelper::setAssesmentHasilAvg($id);
            // CobitHelper::assesmentDfWeight($id);
            // CobitHelper::setCanvasStep2Value($id);
            // CobitHelper::setCanvasStep3Value($id);
            // CobitHelper::updateCanvasAdjust($id);
            SetCanvasHasilDataJob::dispatch($id);

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function canvas(Request $request)
    {

        $_assesment=Assesment::where('id',$request->assesment_id)->exists();
        if(!$_assesment)
        {
            return $this->errorResponse('Assesment tidak terdaftar',404);
        }

        $hasil= Domain::with([
            // 'assesmenthasil',
            // 'assesmenthasil.designfaktor',
            'assesmentcanvas',
        ])
            // ->whereRelation('assesmenthasil','assesment_id', $request->assesment_id)
            ->whereRelation('assesmentcanvas', 'assesment_id', $request->assesment_id)
            ->orderBy('urutan', 'ASC')
            ->get();


        $_hasil=[];
        if(!$hasil->isEmpty())
        {
            foreach ($hasil as $_item_hasil) {
                $hasil_init=$_item_hasil;

                $ass_hasil=AssesmentHasil::where('assesment_id',$request->assesment_id)
                    ->where('domain_id',$_item_hasil->id)
                    ->get();

                $hasil_init['assesmenthasil']=$ass_hasil;
                $_hasil[]=$hasil_init;
            }
        }
        $weight=AssesmentDesignFaktorWeight::with(['designfaktor'])
            ->where('assesment_id', $request->assesment_id)
            ->get();

        $df=DesignFaktor::with('assesmentweight')
            ->whereRelation('assesmentweight','assesment_id', $request->assesment_id)
            ->orderBy('urutan','ASC')
            ->get();

        // $data['hasil'] = DomainCanvasResource::collection($hasil);
        $data['hasil']=$_hasil;
        // $data['weight'] = AssesmentDesignFaktorWeightCanvasResource::collection($weight);
        $data['df'] = DesignFaktorCanvasResource::collection($df);
        return $this->successResponse($data);
    }

    public function setValueAdjustmentBACKUP(AdjustmenValueCanvasRequest $request)
    {
        $request->validated();
        $data=AssesmentCanvas::find($request->id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        $data->adjustment=$request->nilai;
        $data->save();

        return $this->successResponse();
    }

    public function setValueAdjustment(AdjustmenValueCanvasRequest $request)
    {
        $request->validated();

        $payload=$request->data;
        $hasil = $payload['hasil'];
        // $weight=$payload['weight'];
        $df=$payload['df'];

        $tes=[];
        DB::beginTransaction();
        try {
            if (count($hasil) > 0) {
                foreach ($hasil as $_item_hasil) {
                    $assesmentcanvas = $_item_hasil['assesmentcanvas'];
                    $_id = $assesmentcanvas['id'];
                    $_adjustment = $assesmentcanvas['adjustment'];
                    $_reason = $assesmentcanvas['reason'];
                    $_reason_adjst = $assesmentcanvas['reason_adjustment'];

                    $_adjust = AssesmentCanvas::find($_id);
                    $_adjust->adjustment = $_adjustment;
                    $_adjust->reason = $_reason;
                    $_adjust->reason_adjustment = $_reason_adjst;
                    $_adjust->save();
                }
            }

            if (count($df) > 0) {
                foreach ($df as $_item_df) {
                    $_item_weight=$_item_df['assesmentweight'];
                    $_id = $_item_weight['id'];
                    $_n = $_item_weight['weight'];
                    $_weight = AssesmentDesignFaktorWeight::find($_id);
                    $_weight->weight = (float)$_n;
                    $_weight->save();

                    // $tes[]=array(
                    //     // 'item'=>$_item_weight,
                    //     // 'id'=>$_id,
                    //     // 'n'=>$_n,
                    //     'w'=>$_weight,
                    // );
                }
            }

            SetCanvasHasilDataJob::dispatch($request->assement_id);

            DB::commit();
            return $this->successResponse($tes);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    // set nilai
    public function setValueWeight(AdjustmenWeightValueRequest $request)
    {
        $request->validated();
        $data=AssesmentDesignFaktorWeight::where('assesment_id',$request->assesment_id)
            ->where('design_faktor_id',$request->design_faktor_id)
            ->first();

        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        $data->weight=$request->weight;
        $data->save();

        return $this->successResponse();
    }
}
