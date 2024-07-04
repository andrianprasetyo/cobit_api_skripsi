<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quisioner\QuisionerFinishRequest;
use App\Http\Requests\Quisioner\QuisionerSaveAnswerRequest;
use App\Http\Requests\Quisioner\QuisionerStartRequest;
use App\Http\Resources\Answer\GrupAnswerResource;
use App\Http\Resources\AssesmentUsersResource;
use App\Http\Resources\Ref\DivisiRefResource;
use App\Http\Resources\Ref\JabatanRefResource;
use App\Jobs\SetCanvasHasilDataJob;
use App\Jobs\SetProsesQuisionerHasilQueue;
use App\Models\Assesment;
use App\Models\AssessmentQuisioner;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\OrganisasiDivisi;
use App\Models\OrganisasiDivisiJabatan;
use App\Models\Quisioner;
use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerHasil;
use App\Models\QuisionerJawaban;
use App\Models\QuisionerPertanyaan;
use App\Models\AssessmentUsers;
use App\Models\QusisionerHasilAvg;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuisionerController extends Controller
{
    use JsonResponse;
    public function detailRespondenByCode(Request $request)
    {
        $responden = AssessmentUsers::with(['assesment.organisasi','divisi','jabatan'])
            ->withCount('assesmentquisionerhasil')
            ->where('code',$request->get('code'))
            ->first();

        if(!$responden)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        // if(Carbon::now()->gte($responden->assesment->start_date))
        // {
        //     return $this->errorResponse('Assesment quisoner dimulai pada '. $responden->assesment->start_date, 404);
        // }

        // if (Carbon::now()->gte($responden->assesment->end_date))
        // {
        //     return $this->errorResponse('Assesment quisoner sudah selesai pada ' . $responden->assesment->end_date, 404);
        // }

        return $this->successResponse($responden);
    }

    public function start(QuisionerStartRequest $request)
    {

        $request->validated();

        $quisioner = Quisioner::where('aktif',true)->first();
        // $quisioner = Quisioner::find($request->quisioner_id);
        if(!$quisioner)
        {
            return $this->errorResponse('Quisioner tidak di temukan',400);
        }

        $responden= AssessmentUsers::with(['assesment.organisasi'])->find($request->id);
        if ($responden->is_proses == 'done')
        {
            return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
        }

        DB::beginTransaction();
        try {
            $responden->nama = $request->nama;
            $responden->jabatan_id = $request->jabatan_id;
            $responden->divisi_id = $request->divisi_id;
            $responden->status = 'active';
            // $responden->code=null;
            $responden->quesioner_processed=true;
            $responden->save();

            $quisioner_responden = new AssessmentQuisioner();
            $quisioner_responden->assesment_id = $request->assesment_id;
            $quisioner_responden->quisioner_id = $quisioner->id;
            $quisioner_responden->organisasi_id = $responden->assesment->organisasi_id;
            $quisioner_responden->allow = true;
            $quisioner_responden->save();

            DB::commit();
            return $this->successResponse($responden);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function reset(QuisionerStartRequest $request)
    {
        $request->validated([
            'responden_id'=>'required',
            'assesment_id'=>'required',
        ]);

        $id=$request->responden_id;
        $quisioner = Quisioner::where('aktif', true)->first();
        // $quisioner = Quisioner::find($request->quisioner_id);
        if (!$quisioner) {
            return $this->errorResponse('Quisioner tidak di temukan', 400);
        }

        $responden = AssessmentUsers::with(['assesment.organisasi'])->find($id);
        if ($responden->is_proses == 'done') {
            return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
        }

        DB::beginTransaction();
        try {

            $responden->status = 'diundang';
            $responden->quesioner_processed = false;
            $responden->save();

            AssessmentQuisioner::where('assesment_id',$request->assesment_id)
                ->where('quisioner_id', $quisioner->id)
                ->where('organisasi_id', $responden->assesment->organisasi_id)
                ->where('allow',true)
                ->delete();

            QuisionerHasil::where('assesment_users_id',$id)->delete();

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage());
        }
    }

    public function listquestion(Request $request,$id)
    {
        $offset = $request->get('question', 1);
        $limit=1;
        $page = ($offset * $limit) - $limit;

        $user_assesment=AssessmentUsers::with(['assesment','assesmentquisioner','divisi.mapsdf'])->find($id);
        if(!$user_assesment)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        if($user_assesment->status == 'diundang')
        {
            return $this->errorResponse('Status masih pending, harap lengkapi data untuk mengikuti quisioner',400);
        }

        if ($user_assesment->is_proses == 'done')
        {
            return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
        }


        // return $this->successResponse($user_assesment);

        // $user_quisioner=AssessmentQuisioner::where('')
        // $list=DesignFaktor::with(['komponen', 'pertanyaan.grup.jawabans', 'pertanyaan.quisioner'])
        //     ->whereRelation('pertanyaan','quisioner_id',$user_assesment->assesmentquisioner->quisioner_id);


        $list_df = DB::table('design_faktor')
            ->select(
                    'design_faktor.*',
                    'quisioner_pertanyaan.id as quisioner_pertanyaan_id',
                'quisioner_pertanyaan.quisioner_grup_jawaban_id',
                'quisioner_pertanyaan.pertanyaan',
                'quisioner_pertanyaan.quisioner_id',
                'quisioner_pertanyaan.sorting',
                )
            ->join('quisioner_pertanyaan','design_faktor.id','=','quisioner_pertanyaan.design_faktor_id')
            ->where('quisioner_pertanyaan.quisioner_id', $user_assesment->assesmentquisioner->quisioner_id)
            ->whereNull('design_faktor.deleted_at')
            ->whereNull('quisioner_pertanyaan.deleted_at')
            ->whereIn('design_faktor.id', function ($q) use ($user_assesment) {
                $q->select('design_faktor_id')
                    ->from('organisasi_divisi_map_df')
                    ->where('organisasi_divisi_id', $user_assesment->divisi_id);
            });
        $list_df->orderBy('design_faktor.urutan', 'ASC');
        $list_df->orderBy('quisioner_pertanyaan.sorting','ASC');

        $total = $list_df->count();
        $list_df->limit($limit);
        $list_df->skip($page);

        $list_data_df=$list_df->get();

        $list_data=[];
        if(!$list_data_df->isEmpty())
        {
            foreach ($list_data_df as $_item_df) {
                // $_item_df->terisi='wkwkwkwk';
                // $df=$_item_df;
                $komponen=DesignFaktorKomponen::where('design_faktor_id',$_item_df->id)
                    ->orderBy('urutan','ASC')
                    ->get();

                // $list_data_df->komponen= $komponen;
                $list_komponen=[];
                $grup = QuisionerGrupJawaban::with('jawabans')->find($_item_df->quisioner_grup_jawaban_id);

                // $_item_df->grup = new GrupAnswerResource($grup);
                $_item_df->grup= $grup;

                if(!$komponen->isEmpty())
                {
                    foreach ($komponen as $_item_komponen) {
                        // $komp=$_item_komponen;
                        // $komp->grup = $grup;
                        // $_item_komponen->grup=$grup;

                        $jawabans=QuisionerJawaban::with('grup')
                            ->where('quisioner_grup_jawaban_id', $_item_df->quisioner_grup_jawaban_id)
                            ->orderBy('sorting','ASC')
                            ->get();

                        // $komp->grup->jawabans=$jawabans;
                        // $list_komponen[] = $komp;
                        $jawabanss=[];
                        if(!$jawabans->isEmpty())
                        {
                            foreach ($jawabans as $_item_jawaban) {

                                // $j=$_item_jawaban;
                                $_jawaban=QuisionerHasil::select('bobot')
                                    ->where('assesment_users_id', $id)
                                    ->where('quisioner_id',$_item_df->quisioner_id)
                                    ->where('quisioner_pertanyaan_id',$_item_df->quisioner_pertanyaan_id)
                                    ->where('design_faktor_komponen_id', $_item_komponen->id)
                                    ->where('jawaban_id', $_item_jawaban->id)
                                    ->first();

                                $_item_jawaban->hasil=null;
                                if($_jawaban)
                                {
                                    $_item_jawaban->hasil = $_jawaban->bobot;
                                }

                                // $params = array(
                                //     'assesment_users_id' => $id,
                                //     'quisioner_id' => $user_assesment->assesmentquisioner->quisioner_id,
                                //     'quisioner_pertanyaan_id' => $_item_df->quisioner_pertanyaan_id,
                                //     'design_faktor_komponen_id' => $_item_komponen->id,
                                //     'jawaban_id' => $_item_jawaban->id,
                                // );
                                // $_item_jawaban->params = $params;
                                $jawabanss[] = $_item_jawaban;
                            }

                            // $_item_komponen->grup->jawabans = $jawabanss;
                            $_item_komponen->jawabans=$jawabanss;
                        }
                        // $list_komponen

                        $list_komponen[] = $_item_komponen;
                    }
                    $_item_df->komponen = $list_komponen;
                }

                // $jawaban=QuisionerHasil::where('assesment_users_id',$id)
                //     ->where('quisioner_pertanyaan_id',$_item_df->quisioner_pertanyaan_id)
                //     ->where('design_faktor_komponen_id', $komponen->id)
                //     ->first();

                // $df->jawaban=$jawaban;
                $list_data[]=$_item_df;
            }
        }

        // $data['terisi']=array('7684158c-e10b-42c1-96df-a6311d8840bf');
        $meta['total_page'] = ceil($total / $limit);
        $meta['current_page'] = (int) $offset;
        $data['list'] = $list_data;
        $meta['total'] = $total;
        $data['meta'] = $meta;
        // $data = $this->paging($list, 1, $page);
        return $this->successResponse($data);
    }

    public function saveJawaban(QuisionerSaveAnswerRequest $request)
    {

        $request->validated();

        DB::beginTransaction();
        try {

            $list_jawaban=[];
            foreach ($request->hasil as $_item_hasil) {
                // $komponen = $_item_hasil['komponen'];
                $quisioner_id = $_item_hasil['quisioner_id'];
                $quisioner_pertanyaan_id=$_item_hasil['quisioner_pertanyaan_id'];

                foreach ($_item_hasil['komponen'] as $_item_komponen) {

                    foreach ($_item_komponen['jawabans'] as $_item_grup) {

                        $list_jawaban[] = array(
                            'assesment_user_id' => $request->assesment_user_id,
                            'quisioner_id' => $quisioner_id,
                            'quisioner_pertanyaan_id' => $quisioner_pertanyaan_id,
                            'design_faktor_komponen_id' => $_item_komponen['id'],
                            'quisioner_jawaban_id' => $_item_grup['id'],
                            'hasil' => $_item_grup['hasil'],
                        );

                        if($_item_grup['hasil'] != null)
                        {
                            $save=QuisionerHasil::firstOrNew([
                                'quisioner_id'=> $quisioner_id,
                                'quisioner_pertanyaan_id'=> $quisioner_pertanyaan_id,
                                'assesment_users_id'=> $request->assesment_user_id,
                                'design_faktor_komponen_id'=> $_item_komponen['id'],
                            ]);

                            $save->quisioner_id = $quisioner_id;
                            $save->quisioner_pertanyaan_id = $quisioner_pertanyaan_id;
                            $save->jawaban_id = $_item_grup['id'];
                            $save->assesment_users_id = $request->assesment_user_id;
                            $save->design_faktor_komponen_id = $_item_komponen['id'];
                            $save->bobot = $_item_grup['hasil'];
                            $save->save();
                        }
                    }
                }
            }

            DB::commit();
            return $this->successResponse($list_jawaban);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function finish(QuisionerFinishRequest $request)
    {
        $request->validated();

        DB::beginTransaction();
        try {
            $assesment_user_id = $request->assesment_user_id;
            $responden = AssessmentUsers::with(['assesment', 'assesmentquisioner'])->find($assesment_user_id);
            // $assesment=Assesment::find($responden->assesment_id);
            // if (!$responden) {
            //     return $this->errorResponse('Data tidak ditemukan', 404);
            // }

            // if (!$assesment) {
            //     return $this->errorResponse('Assesment tidak ditemukan', 404);
            // }

            if ($responden->status == 'diundang') {
                return $this->errorResponse('Status masih pending, harap lengkapi data untuk mengikuti quisioner', 400);
            }

            if ($responden->status == 'done') {
                return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
            }

            $total_soal = DB::table('design_faktor')
                ->join('quisioner_pertanyaan', 'design_faktor.id', '=', 'quisioner_pertanyaan.design_faktor_id')
                ->where('quisioner_pertanyaan.quisioner_id', $responden->assesmentquisioner->quisioner_id)
                ->whereNull('design_faktor.deleted_at')
                ->whereNull('quisioner_pertanyaan.deleted_at')
                ->count();

            $total_jawaban = QuisionerHasil::where('assesment_users_id', $assesment_user_id)
                ->where('quisioner_id', $responden->assesmentquisioner->quisioner_id)
                ->count();

            if ($total_jawaban < $total_soal) {
                return $this->errorResponse('Harap isi semua jawaban di setiap pertanyaan', 400);
            }

            $responden->status = 'done';
            $responden->is_proses = null;
            $responden->save();

            // SetProsesQuisionerHasilQueue::dispatch($responden->assesment_id);
            SetProsesQuisionerHasilQueue::dispatch($assesment_user_id);
            SetCanvasHasilDataJob::dispatch($responden->assesment_id);

            // $data['total_soal'] = $total_soal;
            // $data['total_jawaban'] = $total_jawaban;

            DB::commit();
            return $this->successResponse(null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function navigation(Request $request)
    {

        $assesment_id=$request->assesment_id;
        $responden_id = $request->responden_id;

        $user_assesment = AssessmentUsers::find($responden_id);
        if (!$user_assesment) {
            return $this->errorResponse('Responden tidak terdaftar', 404);
        }

        $qry_list_pertanyan = DB::table('design_faktor')
            ->select(
                'quisioner_pertanyaan.id',
                'quisioner_pertanyaan.pertanyaan',
                )
            ->join('quisioner_pertanyaan','design_faktor.id','=','quisioner_pertanyaan.design_faktor_id')
            // ->where('quisioner_pertanyaan.quisioner_id', $user_assesment->assesmentquisioner->quisioner_id)
            ->whereNull('design_faktor.deleted_at')
            ->whereNull('quisioner_pertanyaan.deleted_at')
            ->orderBy('design_faktor.urutan', 'ASC')
            ->orderBy('quisioner_pertanyaan.sorting','ASC')
            ->whereIn('design_faktor.id',function($q) use($user_assesment){
                $q->select('design_faktor_id')
                    ->from('organisasi_divisi_map_df')
                    ->where('organisasi_divisi_id',$user_assesment->divisi_id);
            })
            ->get();


        $list_pertanyaan=[];
        $list_terisi=[];
        if(!$qry_list_pertanyan->isEmpty())
        {
            $urutan=1;
            foreach ($qry_list_pertanyan as $_item_pertanyaan) {
                $_check_terisi=DB::table('quisioner_hasil')
                    ->join('assesment_users', 'quisioner_hasil.assesment_users_id', '=', 'assesment_users.id')
                    ->join('assesment','assesment_users.assesment_id','=','assesment.id')
                    ->where('assesment.id',$assesment_id)
                    ->where('assesment_users.id',$responden_id)
                    ->where('quisioner_hasil.quisioner_pertanyaan_id',$_item_pertanyaan->id)
                    ->whereNull('quisioner_hasil.deleted_at')
                    ->select('quisioner_hasil.quisioner_pertanyaan_id')
                    ->exists();

                $list_pertanyaan[]=array(
                    'id'=>$_item_pertanyaan->id,
                    'pertanyaan'=>$_item_pertanyaan->pertanyaan,
                    'terisi'=>$_check_terisi,
                    'urutan'=>$urutan
                );

                if($_check_terisi)
                {
                    $list_terisi[]=$_check_terisi;
                }

                $urutan++;
            }
        }

        $meta['total'] = count($list_pertanyaan);
        $meta['terisi']=count($list_terisi);
        $data['pertanyaan']=$list_pertanyaan;
        $data['meta']=$meta;

        return $this->successResponse($data);
    }

    public function listDivisi(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'nama');
        $sortType = $request->get('sortType', 'ASC');
        $search = $request->search;
        $organisasi_id = $request->organisasi_id;

        $list = OrganisasiDivisi::where('organisasi_id', $organisasi_id);

        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page, DivisiRefResource::class);
        return $this->successResponse($data);
    }

    public function listJabatan(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'nama');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $organisasi_divisi_id = $request->divisi_id;

        $list = OrganisasiDivisiJabatan::where('organisasi_divisi_id', $organisasi_divisi_id);

        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page, JabatanRefResource::class);
        return $this->successResponse($data);
    }

    public function setFinish(Request $request)
    {
        $request->validate([
            'assesment_id'=>'required',
            'assesment_user_id'=>'required|array'
        ]);

        DB::beginTransaction();
        try {
            $assesment_user_id = $request->assesment_user_id;

            foreach ($assesment_user_id as $item_user_id) {
                $responden = AssessmentUsers::with(['assesment', 'assesmentquisioner'])->find($item_user_id);
                if($responden){

                    if ($responden->status == 'diundang') {
                        return $this->errorResponse('Terdapat data responden yang statusnya masih diundang, Silahkan cek kembali', 400);
                    }

                    $total_soal = DB::table('design_faktor')
                        ->join('quisioner_pertanyaan', 'design_faktor.id', '=', 'quisioner_pertanyaan.design_faktor_id')
                        ->where('quisioner_pertanyaan.quisioner_id', $responden->assesmentquisioner->quisioner_id)
                        ->whereNull('design_faktor.deleted_at')
                        ->whereNull('quisioner_pertanyaan.deleted_at')
                        ->count();

                    $total_jawaban = QuisionerHasil::where('assesment_users_id', $item_user_id)
                        ->where('quisioner_id', $responden->assesmentquisioner->quisioner_id)
                        ->count();

                    if ($total_jawaban < $total_soal) {
                        return $this->errorResponse('Terdapat data quesionernya yang total jawabannya tidak sesuai', 400);
                    }

                    $responden->status = 'done';
                    $responden->is_proses = null;
                    $responden->quesioner_processed = true;

                    $responden->save();

                    SetProsesQuisionerHasilQueue::dispatch($item_user_id);
                    SetCanvasHasilDataJob::dispatch($responden->assesment_id);
                }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function updateQuesioner(Request $request,$id)
    {
        $responden = AssessmentUsers::find($id);
        if(!$responden){
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        $responden->update($request->all());
        return $this->successResponse($responden);
    }

    public function updateListAssesmentUser(Request $request)
    {
        $request->validate([
            'assesment_id' => 'required|exists:assesment,id',
            'data'=>'required|array'
        ]);

        DB::beginTransaction();
        try {

            $list_selected=[];
            $not_in_selected = [];

            foreach ($request->data as $item_data){
                $not_in_selected[]=$item_data['id'];
                $list_selected[] = array(
                    'id' => $item_data['id'],
                    'quesioner_processed' => $item_data['quesioner_processed']
                );
            }

            $list_responden = AssessmentUsers::where('assesment_id', $request->assesment_id)
                ->where('quesioner_processed', true)
                ->whereNotIn('id',$not_in_selected)
                ->get();


            if (!$list_responden->isEmpty()) {
                foreach ($list_responden as $item_responden) {
                    $list_selected[] = array(
                        'id' => $item_responden->id,
                        'quesioner_processed' => $item_responden->quesioner_processed
                    );
                }
            }

            foreach ($list_selected as $item_user) {
                $responden = AssessmentUsers::find($item_user['id']);
                if ($responden) {
                    $responden->quesioner_processed = $item_user['quesioner_processed'];
                    $responden->save();

                    // QusisionerHasilAvg::where('assesment_id', $responden->assesment_id)->forceDelete();
                    // DB::table('quisioner_hasil_avg')->where('assesment_id', $responden->assesment_id)->delete();
                    // CobitHelper::getQuisionerHasil($responden->id);
                    // return $this->successResponse($responden);

                    // if ($item_user['quesioner_processed']) {
                    // }
                    SetProsesQuisionerHasilQueue::dispatch($responden->id);
                    SetCanvasHasilDataJob::dispatch($responden->assesment_id);
                }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
