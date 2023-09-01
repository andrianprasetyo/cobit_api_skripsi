<?php

namespace App\Http\Controllers\Api;

use App\Exports\RespondenQuisionerHasilExport;
use App\Exports\QuesionerResultExport;
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
use App\Models\DesignFaktorKomponen;
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

    public function downloadExcel2(Request $request){
        $assesment_id=$request->id;
        $assesmen=Assesment::where('id',$assesment_id)->first();
        $header=DB::select("
            SELECT
                df.ID,
                df.kode,
                df.deskripsi,
                dfk.ID AS dfk_id,
                dfk.nama,
                dfk.deskripsi,
                qp.pertanyaan,
                qp.sorting 
            FROM
                design_faktor_komponen dfk
                JOIN design_faktor df ON df.ID = dfk.design_faktor_id  and df.deleted_at is null and dfk.deleted_at is null
                JOIN quisioner_pertanyaan qp ON qp.design_faktor_id=df.id and qp.deleted_at is null
            ORDER BY
                df.urutan ASC,
                qp.sorting asc,
                dfk.urutan ASC
        ");
        // dd($assesment_id);
        $hasilQuesioner=DB::select("
            SELECT
                *,
                (
                    select json_agg(tbl2) from (
                        select
                            json_build_object(
                                'id',tbl.id,
                                'kode',tbl.kode,
                                'deskripsi',tbl.deskripsi,
                                'dfk_id',tbl.dfk_id,
                                'nama',tbl.nama,
                                'komponen_deskripsi',komponen_deskripsi,
                                'jawaban',jawaban,
                                'bobot',bobot,
                                'pertanyaan',pertanyaan,
                                'assesment_users_id',assesment_users_id,
                                'nama_grup_jawaban',nama_grup_jawaban,
                                'jenis',jenis
                            ) as jawaban
                        from (
                        SELECT
                            df.ID,
                            df.kode,
                            df.deskripsi,
                            dfk.ID AS dfk_id,
                            dfk.nama,
                            dfk.deskripsi as komponen_deskripsi,
                            qj.jawaban,
                            qh.bobot,
                            qp.pertanyaan,
                            qh.assesment_users_id,
                            qgj.nama as nama_grup_jawaban,
                            qgj.jenis 
                        FROM
                            design_faktor_komponen dfk
                            JOIN design_faktor df ON df.ID = dfk.design_faktor_id 
                            left JOIN quisioner_hasil qh ON qh.design_faktor_komponen_id=dfk.id and qh.assesment_users_id=au.id
                            left JOIN quisioner_jawaban qj ON qj.id=qh.jawaban_id
                            left JOIN quisioner_grup_jawaban qgj ON qgj.id=qj.quisioner_grup_jawaban_id
                            left JOIN quisioner_pertanyaan qp ON qp.id=qh.quisioner_pertanyaan_id
                        ORDER BY
                            df.urutan ASC,
                            qp.sorting asc,
                            dfk.urutan ASC
                            
                            ) as tbl
                    
                    ) as tbl2
                ) as jawaban_quesioner 
            FROM
                assesment_users au 
            WHERE
                au.status = 'done'
                and au.assesment_id=:assesment_id
        ",[
            'assesment_id'=>$assesment_id
        ]);
        // return view('report.quesionerresult',[
        //     'header'=>$header,
        //     'hasil'=>$hasilQuesioner
        // ]);
        return Excel::download(new QuesionerResultExport($hasilQuesioner,$header), 'hasilquesioner-'.$assesmen->nama.'.xlsx');
        dd($hasilQuesioner);
    }
    public function downloadExcel(Request $request)
    {
        try {
            // $pertanyaan = QuisionerPertanyaan::orderBy('sorting', 'ASC')->get();
            // return $this->successResponse($pertanyaan);
            // $list = QuisionerHasil::query()->get();
            // // $data= QuisionerHasilResource::collection($list);
            // $data['pertanyaan']=$pertanyaan;
            $komponen_df=DesignFaktorKomponen::orderBy('urutan','ASC')->get();
            $list_pertanyaan=QuisionerPertanyaan::orderBy('sorting','ASC')->get();

            $assesment_id=$request->id;
            $list=DB::table('quisioner_hasil')
                ->join('assesment_users', 'quisioner_hasil.assesment_users_id', '=', 'assesment_users.id')
                ->join('quisioner_pertanyaan', 'quisioner_hasil.quisioner_pertanyaan_id', '=', 'quisioner_pertanyaan.id')
                ->join('design_faktor_komponen', 'quisioner_hasil.design_faktor_komponen_id', '=', 'design_faktor_komponen.id')
                ->join('quisioner_jawaban','quisioner_hasil.jawaban_id','=','quisioner_jawaban.id')
                ->where('assesment_users.assesment_id',$assesment_id)
                ->select(
                        'quisioner_hasil.*',
                        'assesment_users.*',
                        'quisioner_pertanyaan.pertanyaan',
                        'quisioner_pertanyaan.sorting',
                        'design_faktor_komponen.id as komponen_id',
                    'design_faktor_komponen.nama as komponen_nama',
                    'quisioner_jawaban.jawaban',
                )
                ->orderBy('quisioner_pertanyaan.sorting', 'ASC')
                ->orderBy('design_faktor_komponen.urutan','ASC')
                // ->limit(3)
                ->get();
            dd($list);
            $users = [];
            $komponens = [];
            $pertanyaans=[];
            $jawabans=[];

            foreach ($list as $_item_user) {
                $users[]=array(
                    'nama'=>$_item_user->nama,
                    'jabatan' => $_item_user->jabatan,
                    'divisi' => $_item_user->divisi,
                    'komponen' => $_item_user->jabatan,
                );
                $jawabans[]=array(
                    'jawaban_id'=>$_item_user->jawaban_id,
                    'jawaban' => $_item_user->jawaban,
                );
                // $_komponens=QuisionerHasil::where('design_faktor_komponen_id',$_item_user->komponen_id)
                //     ->where('quisioner_pertanyaan_id',$_item_user->quisioner_pertanyaan_id)
                //     ->first();

                // $komponens[]=$_komponens;
            }

            foreach ($list_pertanyaan as $_item_pertanyaan) {
                $pertanyaans[]=array(
                    'id'=>$_item_pertanyaan->id,
                    'pertanyaan' => $_item_pertanyaan->pertanyaan,
                );
            }

            $data['users'] = $users;
            $data['jawabans'] = $jawabans;
            $data['pertanyaan']=$list_pertanyaan;
            $data['komponen']=$komponen_df;
            // return $this->successResponse($data);
            return Excel::download(new RespondenQuisionerHasilExport($data), 'tes.xlsx');
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
