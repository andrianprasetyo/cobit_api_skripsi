<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\Quisioner;
use App\Models\QuisionerPertanyaan;
use App\Models\Responden;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class QuisionerController extends Controller
{
    use JsonResponse;


    public function detailRespondenByEmail(Request $request)
    {
        $responden = Responden::with(['assesment.organisasi'])->where('email',$request->get('email'))->first();
        if(!$responden)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse($responden);
    }

    public function start(Request $request)
    {
        $validate['id']='required|uuid|exists:responden,id';
        $validate_msg['id.required']='Responden ID harus di isi';
        $validate_msg['id.uuid'] = 'Responden ID tidak valid';
        $validate_msg['id.exists'] = 'Responden ID tidak terdaftar';

        $validate['nama']='required';
        $validate_msg['nama.required'] = 'Nama responden harus di isi';

        $request->validate($validate,$validate_msg);

        $id=$request->id;
        $responden=Responden::with(['assesment.organisasi'])->find($id);
        $responden->nama = $request->nama;
        $responden->divisi = $request->divisi;
        $responden->posisi = $request->posisi;
        $responden->save();

        return $this->successResponse($responden);
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
