<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $responden = AssessmentUsers::with(['assesment.organisasi'])->where('code',$request->get('code'))->first();
        if(!$responden)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse(new AssesmentUsersResource($responden));
    }

    public function start(Request $request)
    {
        $validate['id']='required|uuid|exists:assesment_users,id';
        $validate_msg['id.required']='Responden ID harus di isi';
        $validate_msg['id.uuid'] = 'Responden ID tidak valid';
        $validate_msg['id.exists'] = 'Responden ID tidak terdaftar';

        $validate['assesment_id'] = 'required|uuid|exists:assesment,id';
        $validate_msg['assesment_id.required'] = 'Assesment ID harus di isi';
        $validate_msg['assesment_id.uuid'] = 'Assesment ID tidak valid';
        $validate_msg['assesment_id.exists'] = 'Assesment ID tidak terdaftar';

        $validate['nama']='required';
        $validate_msg['nama.required'] = 'Nama responden harus di isi';

        $request->validate($validate,$validate_msg);

        $quisioner = Quisioner::where('aktif', true)->first();

        $id=$request->id;
        $responden= AssessmentUsers::with(['assesment.organisasi'])->find($id);
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
                )
            ->join('quisioner_pertanyaan','design_faktor.id','=','quisioner_pertanyaan.design_faktor_id')
            ->where('')
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
                if(!$komponen->isEmpty())
                {
                    foreach ($komponen as $_item_komponen) {
                        $komp=$_item_komponen;
                        $grup=QuisionerGrupJawaban::with('jawabans')->find($_item_df->quisioner_grup_jawaban_id);
                        $list_komponen[]=$komp;
                        $komp->grup=$grup;
                        $list_komponen[]=$komp;
                    }
                }
                $df->komponen= $list_komponen;
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

    public function saveJawaban(Request $request)
    {
        $validate['quisioner_id'] = 'required|uuid|exists:quisioner,id';
        $validate_msg['quisioner_id.required']='Quisioner ID harus di isi';
        $validate_msg['quisioner_id.uuid'] = 'Quisioner ID tidak valid';
        $validate_msg['quisioner_id.exists'] = 'Quisioner ID tidak terdaftar';

        $validate['quisioner_pertanyaan_id'] = 'required|uuid|exists:quisioner_pertanyaan,id';
        $validate_msg['quisioner_pertanyaan_id.required'] = 'Quisioner Pertanyaan ID harus di isi';
        $validate_msg['quisioner_pertanyaan_id.uuid'] = 'Quisioner Pertanyaan ID tidak valid';
        $validate_msg['quisioner_pertanyaan_id.exists'] = 'Quisioner Pertanyaan ID tidak terdaftar';

        $validate['quisioner_jawaban_id'] = 'required|uuid|exists:quisioner_jawaban,id';
        $validate_msg['quisioner_jawaban_id.required'] = 'Quisioner jawaban ID harus di isi';
        $validate_msg['quisioner_jawaban_id.uuid'] = 'Quisioner jawaban ID tidak valid';
        $validate_msg['quisioner_jawaban_id.exists'] = 'Quisioner jawaban ID tidak terdaftar';

        $validate['assesment_user_id'] = 'required|uuid|exists:assesment_users,id';
        $validate_msg['assesment_user_id.required'] = 'Asessment user ID harus di isi';
        $validate_msg['assesment_user_id.uuid'] = 'Asessment user ID tidak valid';
        $validate_msg['assesment_user_id.exists'] = 'Asessment user ID tidak terdaftar';

        // $validate['responden_id'] = 'required|uuid|exists:assesment_users,id';
        // $validate_msg['responden_id.required'] = 'Responden user ID harus di isi';
        // $validate_msg['responden_id.uuid'] = 'Responden ID tidak valid';
        // $validate_msg['responden_id.exists'] = 'Responden ID tidak terdaftar';

        $validate['design_faktor_komponen_id'] = 'required|uuid|exists:design_faktor_komponen,id';
        $validate_msg['design_faktor_komponen_id.required'] = 'Design faktor ID harus di isi';
        $validate_msg['design_faktor_komponen_id.uuid'] = 'Design faktor ID tidak valid';
        $validate_msg['design_faktor_komponen_id.exists'] = 'Design faktor ID tidak terdaftar';

        $validate['design_faktor_komponen_id'] = 'required|uuid|exists:design_faktor_komponen,id';
        $validate_msg['design_faktor_komponen_id.required'] = 'Design faktor ID harus di isi';
        $validate_msg['design_faktor_komponen_id.uuid'] = 'Design faktor ID tidak valid';
        $validate_msg['design_faktor_komponen_id.exists'] = 'Design faktor ID tidak terdaftar';

        $validate['jenis_grup']='required|in:pilgan,persentase';
        $validate_msg['jenis_grup.required'] = 'Jenis grup jawaban harus di isi';
        $validate_msg['jenis_grup.in'] = 'Jenis grup tidak valid (pilgan,persentase)';

        // $validate['bobot']= 'required|number';
        // $validate['bobot.required'] ='Bobot harus di isi';
        // $validate['bobot.number'] = 'Bobot harus dalam bentuk angka';

        $request->validate($validate,$validate_msg);
        // $_check_jawaban= QuisionerHasil::where('quisioner_id',$request->quisioner_id)
        //     ->where('quisioner_pertanyaan_id',$request->quisioner_pertanyaan_id)
        //     ->where('jawaban_id', $request->quisioner_jawaban_id)
        //     ->where('assesment_users_id', $request->assesment_user_id)
        //     ->where('design_faktor_komponen_id', $request->design_faktor_komponen_id)
        //     ->exists();

        // if($_check_jawaban)
        // {
        //     return $this->errorResponse('Anda sudah mengisi quisioner jawaban ini',400);
        // }

        DB::beginTransaction();
        try {
            $bobot = QuisionerJawaban::find($request->quisioner_jawaban_id);

            // $data = new QuisionerHasil();

            $data=QuisionerHasil::firstOrNew([
                'quisioner_id'=> $request->quisioner_id,
                'quisioner_pertanyaan_id'=> $request->quisioner_pertanyaan_id,
                'assesment_users_id'=> $request->assesment_user_id,
                'design_faktor_komponen_id'=> $request->design_faktor_komponen_id,
            ]);

            $data->quisioner_id = $request->quisioner_id;
            $data->quisioner_pertanyaan_id = $request->quisioner_pertanyaan_id;
            $data->jawaban_id = $request->quisioner_jawaban_id;
            $data->assesment_users_id = $request->assesment_user_id;
            $data->design_faktor_komponen_id = $request->design_faktor_komponen_id;
            $data->bobot = $bobot->bobot;
            $data->save();

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
    // public function add(Request $request)
    // {
    //     $validate['df_kode']='required';
    //     $validate['df_nama'] = 'required';
    //     $validate_msg['df_kode.required']='Kode Design faktor harus di isi';
    //     $validate_msg['df_nama.required'] = 'Nama Design faktor harus di isi';
    //     // $validate_msg['df_kode.unique'] = 'Kode Design faktor sudah digunakan';

    //     $validate['pertanyaan'] = 'required|unique:quisioner,title';
    //     $validate_msg['pertanyaan.required'] = 'Nama pertanyaan harus di isi';
    //     $validate_msg['pertanyaan.unique'] = 'Nama pertanyaan sudah tersedia';

    //     $validate['quisioner_grup_jawaban_id'] = 'required|uuid|exists:quisioner_grup_jawaban,id';
    //     $validate_msg['quisioner_grup_jawaban_id.required'] = 'Grup jawaban harus di isi';
    //     $validate_msg['quisioner_grup_jawaban_id.uuid'] = 'Grup jawaban ID tidak valid';
    //     $validate_msg['quisioner_grup_jawaban_id.exists'] = 'Grup jawaban tidak terdaftar';

    //     $validate['df_komponen'] = 'required|array';
    //     $validate['df_komponen.*.nama'] = 'required';
    //     $validate['df_komponen.*.baseline']='required|integer';

    //     $validate_msg['df_komponen.required'] = 'komponen harus di isi';
    //     $validate_msg['df_komponen.array'] = 'komponen harus dalam bentuk array';

    //     $validate_msg['df_komponen.*.nama.required'] = 'Nama komponen harus di isi';
    //     $validate_msg['df_komponen.*.baseline.required'] = 'Baseline komponen harus di isi';
    //     $validate_msg['df_komponen.*.baseline.integer'] = 'Baseline komponen harus dalam bentuk angka';

    //     $request->validate($validate, $validate_msg);

    //     $quesioner=Quisioner::where('aktif',true)->first();
    //     if(!$quesioner)
    //     {
    //         return $this->errorResponse('Quisioner aktif berlum tersedia',404);
    //     }

    //     $df=DesignFaktor::where('kode',$request->df_kode)->first();
    //     if(!$df)
    //     {
    //         $df=new DesignFaktor();
    //         $df->kode=$request->df_kode;
    //         $df->nama = $request->nama;
    //         $df->deskripsi = $request->deskripsi;
    //         $df->save();
    //     }

    //     foreach ($request->df_komponen as $_item_komponen)
    //     {
    //         $df_komponen=new DesignFaktorKomponen();
    //         $df_komponen->nama = $_item_komponen['nama'];
    //         $df_komponen->baseline = $_item_komponen['baseline'];
    //         $df_komponen->design_faktor_id=$df->id;
    //         $df_komponen->save();
    //     }

    //     foreach ($request as $key => $value) {
    //         # code...
    //     }
    //     $quesioner_pertanyaan=new QuisionerPertanyaan();
    //     $quesioner_pertanyaan->design_faktor_id=$df->id;
    //     $quesioner_pertanyaan->quisioner_id = $quesioner->id;
    //     $quesioner_pertanyaan->quisioner_grup_jawaban_id = $request->quisioner_grup_jawaban_id;
    //     $quesioner_pertanyaan->save();

    //     return $this->successResponse();
    // }
}
