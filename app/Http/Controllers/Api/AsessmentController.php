<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\Organisasi;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class AsessmentController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = Assesment::query();
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if($request->filled('status'))
        {
            $list->where('status',$request->status);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data=Assesment::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse($data);
    }


    public function add(Request $request)
    {
        try {
            $request->validate(
                [
                    'asesment' => 'required',
                    'start_date' => 'required|date_format:Y-m-d',
                    'end_date' => 'required|date_format:Y-m-d|after:start_date',
                    'organisasi' => 'required|unique:organisasi,nama',
                ],
                [
                    'asesment.required' => 'Nama assesment harus di isi',
                    'start_date.required' => 'Waktu awal assesment harus di isi',
                    'start_date.date_format' => 'Waktu awal tidak valid',
                    'end_date.required' => 'Waktu selesai assesment harus di isi',
                    'end_date.date_format' => 'Waktu selesai tidak valid',
                    'end_date.after' => 'Waktu selesai harus setelah waktu mulai',
                    'organisasi.required' => 'Harap pilih organisasi',
                    'organisasi.unique' => 'Organisasi sudah digunakan',
                ]
            );

            $organisasi=new Organisasi();
            $organisasi->nama=$request->organisasi;
            $organisasi->deskripsi=$request->deskripsi;
            $organisasi->save();

            $assesment = new Assesment();
            $assesment->nama = $request->nama;
            $assesment->organisasi_id = $organisasi->id;
            $assesment->start_date = $request->start_date;
            $assesment->end_date = $request->end_date;
            $assesment->status = 'start';
            $assesment->save();

            $user_ass=new AssessmentUsers();
            $user_ass->assesment_id=$assesment->id;
            $user_ass->nama=$request->nama;
            $user_ass->divisi = $request->divisi;
            $user_ass->jabatan = $request->jabatan;
            $user_ass->save();

            return $this->successResponse();
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function edit(Request $request,$id)
    {
        $data = Assesment::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
    }

    public function remove($id)
    {
        $data = Assesment::find($id);
        if (!$data)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
    }
}
