<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\Quisioner;
use App\Models\QuisionerPertanyaan;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DesignFaktorController extends Controller
{
    use JsonResponse;
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'kode');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;

        $list=DesignFaktor::query();
        if ($request->filled('search'))
        {
            $list->where('kode', 'ilike', '%' . $search . '%');
            $list->orWhere('nama', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data=DesignFaktor::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'kode' => 'required|unique:design_faktor,kode',
            ],
            [
                'kode.required' => 'kode harus di isi',
                'kode.unique' => 'Kode sudah digunakan',
            ]
        );

        $data = new DesignFaktor();
        $data->kode = $request->kode;
        $data->nama = $request->nama;
        $data->deskripsi = $request->deskripsi;
        $data->save();

        return $this->successResponse();
    }

    public function edit(Request $request, $id)
    {
        $request->validate(
            [
                'kode' => 'required',
            ],
            [
                'kode.required' => 'kode harus di isi',
            ]
        );

        $data = DesignFaktor::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->nama = $request->nama;
        $data->deskripsi = $request->deskripsi;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = DesignFaktor::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }


    public function addQuisioner(Request $request)
    {
        $validate['df_kode'] = 'required';
        $validate['df_nama'] = 'required';
        $validate_msg['df_kode.required'] = 'Kode Design faktor harus di isi';
        $validate_msg['df_nama.required'] = 'Nama Design faktor harus di isi';
        // $validate_msg['df_kode.unique'] = 'Kode Design faktor sudah digunakan';

        // $validate['pertanyaan'] = 'required|unique:quisioner,title';
        // $validate_msg['pertanyaan.required'] = 'Nama pertanyaan harus di isi';
        // $validate_msg['pertanyaan.unique'] = 'Nama pertanyaan sudah tersedia';

        $validate['question']='required|array';
        $validate_msg['question.required'] = 'Question harus di isi';
        $validate_msg['question.array'] = 'Question harus dalam bentuk array';

        $validate['question.*.grup_id']='required|uuid|exists:quisioner_grup_jawaban,id';
        $validate['question.*.pertanyaan']='required';

        // $validate['quisioner_grup_jawaban_id'] = 'required|uuid|exists:quisioner_grup_jawaban,id';
        $validate_msg['question.*.grup_id.required'] = 'Grup jawaban harus di isi';
        $validate_msg['question.*.grup_id.uuid'] = 'Grup jawaban ID tidak valid';
        $validate_msg['question.*.grup_id.exists'] = 'Grup jawaban tidak terdaftar';
        $validate_msg['question.*.pertanyaan.required'] = 'Pertanyaan harus di isi';

        $validate['df_komponen'] = 'required|array';
        $validate['df_komponen.*.nama'] = 'required';
        $validate['df_komponen.*.baseline'] = 'required|integer';

        $validate_msg['df_komponen.required'] = 'komponen harus di isi';
        $validate_msg['df_komponen.array'] = 'komponen harus dalam bentuk array';

        $validate_msg['df_komponen.*.nama.required'] = 'Nama komponen harus di isi';
        $validate_msg['df_komponen.*.baseline.required'] = 'Baseline komponen harus di isi';
        $validate_msg['df_komponen.*.baseline.integer'] = 'Baseline komponen harus dalam bentuk angka';

        $request->validate($validate, $validate_msg);

        $quesioner = Quisioner::where('aktif', true)->first();
        if (!$quesioner) {
            return $this->errorResponse('Quisioner aktif berlum tersedia', 404);
        }

        $_df = DesignFaktor::where('kode', $request->df_kode)->first();
        if ($_df)
        {
            return $this->errorResponse('Kode '.$request->df_kode.' sudah digunakan',400);
        }

        $df = new DesignFaktor();
        $df->kode = $request->df_kode;
        $df->nama = $request->df_nama;
        $df->deskripsi = $request->df_deskripsi;
        $df->save();

        foreach ($request->df_komponen as $_item_komponen)
        {
            $df_komponen = new DesignFaktorKomponen();
            $df_komponen->nama = $_item_komponen['nama'];
            $df_komponen->baseline = $_item_komponen['baseline'];
            $df_komponen->design_faktor_id = $df->id;
            $df_komponen->save();
        }

        foreach ($request->question as $_item_question)
        {
            # code...
            $quesioner_pertanyaan = new QuisionerPertanyaan();
            $quesioner_pertanyaan->design_faktor_id = $df->id;
            $quesioner_pertanyaan->quisioner_id = $quesioner->id;
            $quesioner_pertanyaan->quisioner_grup_jawaban_id = $_item_question['grup_id'];
            $quesioner_pertanyaan->pertanyaan = $_item_question['pertanyaan'];
            $quesioner_pertanyaan->save();
        }

        return $this->successResponse();
    }

}
