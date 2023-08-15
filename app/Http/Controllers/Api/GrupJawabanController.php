<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerJawaban;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GrupJawabanController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = QuisionerGrupJawaban::query();
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function addGrupJawaban(Request $request)
    {

        $validation['nama']='required|unique:quisioner_grup_jawaban';
        $validation['jenis'] = 'required|in:pilgan,persentase';

        $message['nama.required'] = 'Nama grup harus di isi';
        $message['nama.unique'] = 'Nama sudah digunakan';
        $message['jenis.required'] = 'Jenis grup harus di isi';
        $message['jenis.in']='Jenis grup hanya pilgan|persentase';

        if($request->filled('jawaban'))
        {
            $validation['jawaban'] = 'array';
            $validation['jawaban.*.nama'] = 'required';
            $validation['jawaban.*.bobot']='required|integer';

            $validation['jawaban']='array';
            $message['jawaban.array'] = 'Jawaban harus dalam bentuk array';
            $message['jawaban.*.nama.required'] = 'Pertanyaan harus di isi';
            $message['jawaban.*.bobot.required'] = 'Bobot harus di isi';
            $message['jawaban.*.bobot.integer'] = 'Bobot harus dalam bentuk int';
        }
        $request->validate($validation,$message);

        DB::beginTransaction();
        try {
            $grup = new QuisionerGrupJawaban();
            $grup->nama = $request->nama;
            $grup->jenis = $request->jenis;
            $grup->save();

            $_jawaban = [];
            for ($i = 0; $i < count($request->jawaban); $i++) {
                $_jawaban[] = array(
                    'id'=>Str::uuid(),
                    'quisioner_grup_jawaban_id' => $grup->id,
                    'jawaban' => $request->jawaban[$i]['nama'],
                    'bobot' => $request->jawaban[$i]['bobot'],
                );
            }
            QuisionerJawaban::insert($_jawaban);

            DB::commit();
            return $this->successResponse($grup);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function detailByID($id)
    {
        $data = QuisionerGrupJawaban::with('jawabans')->find($id);
        if (!$data)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function deleteGrupByID($id)
    {
        $data=QuisionerGrupJawaban::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        $data->delete();
    }

    public function editGrup(Request $request,$id)
    {
        $request->validate(
            [
                'nama'=>'required',
                'jenis' => 'required|in:pilgan,persentase',
            ],
            [
                'nama.required'=>'Nama grup harus di isi',
                'jenis.required' => 'Jenis grup harus di isi',
                'jenis.in' => 'Jenis grup hanya pilgan|persentase',
            ]
        );

        $grup = QuisionerGrupJawaban::find($id);
        if (!$grup)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        $grup->nama=$request->nama;
        $grup->jenis = $request->jenis;
        $grup->save();

        return $this->successResponse();
    }

    public function addJawaban(Request $request)
    {
        $request->validate(
            [
                'grupid'=>'required|uuid|exists:quisioner_grup_jawaban,id',
                'nama'=>'required',
                'bobot' => 'required|integer',
            ],
            [

                'grupid.required' => 'Grup jawaban ID harus di isi',
                'grupid.uuid' => 'Grup jawaban ID tidak valid',
                'grupid.exists' => 'Grup jawaban ID tidak terdaftar',
                'nama.required'=>'Jawaban harus di isi',
                'bobot.required' => 'Bobot harus di isi',
                'bobot.integer' => 'Bobot harus berupa integer',
            ]
        );

        $grupid=$request->grupid;
        $grup=QuisionerGrupJawaban::find($grupid);
        if (!$grup)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $jawaban=new QuisionerJawaban();
        $jawaban->jawaban=$request->nama;
        $jawaban->bobot = $request->bobot;
        $jawaban->quisioner_grup_jawaban_id = $grupid;
        $jawaban->save();

        return $this->successResponse();
    }

    public function deleteJawabanID($id)
    {
        $data = QuisionerJawaban::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }

    public function editJawaban(Request $request, $id)
    {
        $request->validate(
            [
                'nama' => 'required',
                'bobot' => 'required|integer',
            ],
            [
                'nama.required' => 'Jawaban harus di isi',
                'bobot.required' => 'Bobot harus di isi',
                'bobot.integer' => 'Bobot harus berupa integer',
            ]
        );

        $jawaban = QuisionerJawaban::find($id);
        if (!$jawaban)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $jawaban->jawaban = $request->nama;
        $jawaban->bobot = $request->bobot;
        $jawaban->save();

        return $this->successResponse();
    }
}
