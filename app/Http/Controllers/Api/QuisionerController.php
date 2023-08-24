<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quisioner\QuisionerFinishRequest;
use App\Http\Requests\Quisioner\QuisionerSaveAnswerRequest;
use App\Http\Requests\Quisioner\QuisionerStartRequest;
use App\Http\Resources\Answer\GrupAnswerResource;
use App\Http\Resources\AssesmentUsersResource;
use App\Models\AssessmentQuisioner;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\Quisioner;
use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerHasil;
use App\Models\QuisionerJawaban;
use App\Models\QuisionerPertanyaan;
use App\Models\AssessmentUsers;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuisionerController extends Controller
{
    use JsonResponse;
    public function detailRespondenByCode(Request $request)
    {
        $responden = AssessmentUsers::with(['assesment.organisasi'])
            ->withCount('assesmentquisionerhasil')
            ->where('code',$request->get('code'))
            ->first();

        if(!$responden)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse($responden);
    }

    public function start(QuisionerStartRequest $request)
    {

        $request->validated();

        $quisioner = Quisioner::where('aktif',true)->first();
        // $quisioner = Quisioner::find($request->quisioner_id);
        // if($quisioner->aktif)
        // {
        //     return $this->errorResponse('Quisioner')
        // }
        $responden= AssessmentUsers::with(['assesment.organisasi'])->find($request->id);
        if ($responden->is_proses == 'done')
        {
            return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
        }

        DB::beginTransaction();
        try {
            $responden->nama = $request->nama;
            $responden->divisi = $request->divisi;
            $responden->jabatan = $request->jabatan;
            $responden->jabatan = $request->jabatan;
            $responden->status = 'active';
            // $responden->code=null;
            $responden->save();

            $quisioner_responden = new AssessmentQuisioner();
            $quisioner_responden->assesment_id = $request->assesment_id;
            $quisioner_responden->quisioner_id = $request->quisioner_id;
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

    public function listquestion(Request $request,$id)
    {
        $offset = $request->get('question', 1);
        $limit=1;
        $page = ($offset * $limit) - $limit;

        $user_assesment=AssessmentUsers::with(['assesment','assesmentquisioner'])->find($id);
        if(!$user_assesment)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        if($user_assesment->status == 'pending')
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
                )
            ->join('quisioner_pertanyaan','design_faktor.id','=','quisioner_pertanyaan.design_faktor_id')
            ->where('quisioner_pertanyaan.quisioner_id', $user_assesment->assesmentquisioner->quisioner_id)
            ->whereNull('design_faktor.deleted_at')
            ->whereNull('quisioner_pertanyaan.deleted_at');

        $list_df->orderBy('design_faktor.sorting', 'ASC');
        $list_df->orderBy('quisioner_pertanyaan.sorting','ASC');

        $total = $list_df->count();
        $list_df->limit($limit);
        $list_df->skip($page);

        $list_data_df=$list_df->get();

        $list_data=[];
        if(!$list_data_df->isEmpty())
        {
            foreach ($list_data_df as $_item_df) {
                $df=$_item_df;
                $komponen=DesignFaktorKomponen::where('design_faktor_id',$_item_df->id)->get();
                $list_komponen=[];

                $grup=QuisionerGrupJawaban::with('jawabans')->find($_item_df->quisioner_grup_jawaban_id);
                $df->grup=new GrupAnswerResource($grup);

                if(!$komponen->isEmpty())
                {
                    foreach ($komponen as $_item_komponen) {
                        $komp=$_item_komponen;

                        // $list_komponen[]=$komp;
                        $jawabans=[];
                        if(count($grup->jawabans) > 0)
                        {
                            foreach ($grup->jawabans as $_item_jawaban) {

                                $_jawaban=QuisionerHasil::select('bobot')
                                    ->where('assesment_users_id', $id)
                                    ->where('quisioner_id',$_item_df->quisioner_id)
                                    ->where('quisioner_pertanyaan_id',$_item_df->quisioner_pertanyaan_id)
                                    ->where('design_faktor_komponen_id', $_item_komponen->id)
                                    ->where('jawaban_id', $_item_jawaban->id)
                                    ->first();

                                $_hasil=null;
                                if($_jawaban)
                                {
                                    $_hasil=$_jawaban->bobot;
                                }
                                $_item_jawaban->hasil=$_hasil;
                                $jawabans[]=$_item_jawaban;
                            }
                            $grup->jawabans=$jawabans;
                        }
                        $komp->grup = $grup;
                        $list_komponen[]=$komp;
                    }
                }

                $df->komponen= $list_komponen;

                // $jawaban=QuisionerHasil::where('assesment_users_id',$id)
                //     ->where('quisioner_pertanyaan_id',$_item_df->quisioner_pertanyaan_id)
                //     ->where('design_faktor_komponen_id', $komponen->id)
                //     ->first();

                // $df->jawaban=$jawaban;
                $list_data[]=$df;
            }
        }

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

                    foreach ($_item_komponen['grup']['jawabans'] as $_item_grup) {

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
        $assesment_user_id=$request->assesment_user_id;
        $responden=AssessmentUsers::with(['assesment','assesmentquisioner'])->find($assesment_user_id);
        if(!$responden)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        if ($responden->status == 'pending') {
            return $this->errorResponse('Status masih pending, harap lengkapi data untuk mengikuti quisioner', 400);
        }

        if ($responden->is_proses == 'done') {
            return $this->errorResponse('Anda sudah melakukan pengisian quisioner', 400);
        }

        $total_soal = DB::table('design_faktor')
            ->join('quisioner_pertanyaan','design_faktor.id','=','quisioner_pertanyaan.design_faktor_id')
            ->where('quisioner_pertanyaan.quisioner_id', $responden->assesmentquisioner->quisioner_id)
            ->whereNull('design_faktor.deleted_at')
            ->whereNull('quisioner_pertanyaan.deleted_at')
            ->count();

        $total_jawaban=QuisionerHasil::where('assesment_users_id',$assesment_user_id)
            ->where('quisioner_id', $responden->assesmentquisioner->quisioner_id)
            ->count();

        if($total_jawaban < $total_soal)
        {
            return $this->errorResponse('Harap isi semua jawaban di setiap pertanyaan',400);
        }

        $responden->status='done';
        $responden->is_proses = 'done';
        $responden->save();

        $data['total_soal'] = $total_soal;
        $data['total_jawaban']=$total_jawaban;
        return $this->successResponse($data);
    }
}
