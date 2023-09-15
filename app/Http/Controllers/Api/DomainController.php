<?php

namespace App\Http\Controllers\Api;

use App\Exports\AssesmentDomain2Export;
use App\Exports\AssesmentDomainExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Chart\ChartDomainResource;
use App\Http\Resources\Domain\DomainByAssesmentResource;
use App\Models\Assesment;
use App\Models\AssesmentCanvas;
use App\Models\AssesmentHasil;
use App\Models\CapabilityLevel;
use App\Models\DesignFaktor;
use App\Models\Domain;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DomainController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'urutan');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;

        $list = Domain::query();
        if ($request->filled('search')) {
            $list->where('kode', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = Domain::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'kode'=>'required|unique:domain,kode',
            ],
            [
                'kode.required'=>'kode harus di isi',
                'kode.unique' => 'Kode sudah digunakan',
            ]
        );

        $data=New Domain();
        $data->kode =$request->kode;
        $data->ket = $request->ket;
        $data->translate=$request->translate;
        $data->save();

        return $this->successResponse();
    }

    public function edit(Request $request,$id)
    {
        $request->validate(
            [
                'kode' => 'required',
            ],
            [
                'kode.required' => 'kode harus di isi',
            ]
        );

        $data = Domain::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak di temukan',404);
        }
        $data->kode = $request->kode;
        $data->ket = $request->ket;
        $data->translate = $request->translate;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = Domain::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }

    public function listDomainByAssesment(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'domain.urutan');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $assesment_id = $request->assesment_id;

        // $list = AssesmentCanvas::with(['domain'])
        //     ->where('assesment_id', $assesment_id);

        $list=DB::table('assesment_canvas')
            ->join('domain','assesment_canvas.domain_id','=','domain.id')
            ->where('assesment_canvas.assesment_id',$assesment_id)
            ->whereNull('domain.deleted_at')
            ->select('assesment_canvas.*','domain.kode','domain.ket','domain.urutan');

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page, DomainByAssesmentResource::class);
        return $this->successResponse($data);
    }

    public function exportDomainByAssesment(Request $request)
    {
        $id=$request->id;
        $assesment = Assesment::find($id);
        // $data=AssesmentCanvas::with(['assesment','domain'])->where('assesment_id',$id)->get();
        $data = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->join('assesment', 'assesment_canvas.assesment_id', '=', 'assesment.id')
            ->select('assesment_canvas.*', 'domain.kode', 'domain.ket', 'domain.urutan','assesment.minimum_target')
            ->where('assesment_canvas.assesment_id', $id)
            ->whereNull('domain.deleted_at')
            ->whereNull('assesment.deleted_at')
            ->orderBy('domain.urutan','ASC')
            ->get();

        if (!$assesment)
        {
            return $this->errorResponse('Assesment ID tidak terdaftar', 404);
        }

        return Excel::download(new AssesmentDomainExport($data), 'Domain-Assesment-' . $assesment->nama . '.xlsx');
    }

    public function exportDomainAdjustmentByAssesment(Request $request)
    {
        $id = $request->id;
        $assesment = Assesment::find($id);
        // $data = AssesmentCanvas::with(['assesment', 'domain'])->where('assesment_id', $id)->get();

        $data = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->select('assesment_canvas.*', 'domain.kode', 'domain.ket', 'domain.urutan')
            ->where('assesment_canvas.assesment_id', $id)
            ->whereNull('domain.deleted_at')
            ->orderBy('domain.urutan', 'ASC')
            ->get();

        if (!$assesment) {
            return $this->errorResponse('Assesment ID tidak terdaftar', 404);
        }

        return Excel::download(new AssesmentDomain2Export($data), 'Domain-Assesment-' . $assesment->nama . '.xlsx');
    }

    public function chartDomainResultBACKUP(Request $request)
    {
        $assesment_id=$request->assesment_id;
        $data=DB::select("
            select (
            SELECT json_agg(domain.kode) FROM (
            select kode,urutan from domain ORDER BY urutan desc) as domain
            ) as domain,
            (
            select json_agg(canvas.step2_value) as step2_value from (
            SELECT * FROM assesment_canvas ah JOIN domain d ON d.id=ah.domain_id WHERE assesment_id='$assesment_id' ORDER BY d.urutan desc) as canvas
            ) as canvas_step2,
            (select json_agg(canvas.step3_value) as step2_value from (
            SELECT * FROM assesment_canvas ah JOIN domain d ON d.id=ah.domain_id WHERE assesment_id='$assesment_id' ORDER BY d.urutan desc) as canvas
            ) as canvas_step3
        ");

        return $this->successResponse($data);
    }

    public function chartDomainResult(Request $request)
    {
        $_list_domain=Domain::orderBy('urutan','ASC')->get();

        $series = [];
        $categories = [];

        $series_step2 = [];
        $series_step3 = [];
        if(!$_list_domain->isEmpty())
        {
            $_step2 = [];
            $_step3 = [];

            foreach ($_list_domain as $_item_domain) {
                $categories[]=$_item_domain->kode;

                $n=AssesmentCanvas::where('domain_id',$_item_domain->id)
                    ->where('assesment_id', $request->assesment_id)
                    ->first();

                if($n){
                    $_step2[] = $n->step2_value;
                    $_step3[] = $n->step3_value;
                }else{
                    $_step2[] = 0;
                    $_step3[] = 0;
                }
            }

            $series_step2 = array(
                [
                    'name' => 'Step 2: Determine the initial scope of the Governance System',
                    'data' => $_step2,
                ]
            );

            $series_step3 = array(
                [
                    'name' => 'Step 3: Refine the scope of the Governance System',
                    'data' => $_step3,
                ]
            );
        }

        $series=array(
            'step_2'=>$series_step2,
            'step_3' => $series_step3,
        );

        $data['categories'] = $categories;
        $data['series'] = $series;

        return $this->successResponse($data);
    }

    public function chartDomainAdjustmentResult(Request $request)
    {
        $_list_domain = Domain::orderBy('urutan', 'ASC')->get();

        $series = [];
        $categories = [];

        if(!$_list_domain->isEmpty())
        {
            $_step3 = [];
            $_adjustmen = [];
            foreach ($_list_domain as $_item_domain)
            {
                $categories[]=$_item_domain->kode;
                $n = AssesmentCanvas::where('domain_id', $_item_domain->id)
                    ->where('assesment_id', $request->assesment_id)
                    ->first();

                if($n){
                    $_step3[] = $n->step3_value;
                    $_adjustmen[]=(int) $n->step3_value + $n->adjustment;
                }else{
                    $_step3[]=0;
                    $_adjustmen[] = 0;
                }
            }

            $series = array(
                [
                    'name'=>'Step 3: Refine the scope of the Governance System',
                    'data'=>$_step3
                ],
                [
                    'name' => 'Step 4: Conclude the Scope of the Governance System',
                    'data' => $_adjustmen
                ]
            );
        }

        $data['categories'] = $categories;
        $data['series'] = $series;

        return $this->successResponse($data);
    }

    public function listDomainByAssesmentCapable(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'domain.urutan');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $assesment_id = $request->assesment_id;

        // $list = AssesmentCanvas::with(['domain'])
        //     ->where('assesment_id', $assesment_id);

        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        $list = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->select('domain.id','domain.kode')
            ->where('assesment_canvas.assesment_id', $assesment_id)
            ->where('assesment_canvas.aggreed_capability_level','>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at');

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function listLevelByDomainCapable(Request $request)
    {
        $list=CapabilityLevel::select('level')
            ->where('domain_id',$request->domain_id)
            ->groupBy('level')
            // ->orderBy('urutan', 'ASC')
            ->get();


        $data=[];
        if(!$list->isEmpty()){

            $collect=collect($list);
            $data=$collect->sortBy('level')->values()->all();
        }

        return $this->successResponse($data);
    }
}
