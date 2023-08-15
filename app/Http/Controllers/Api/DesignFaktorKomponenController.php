<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DesignFaktorKomponenController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'nama');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;
        $df_id = $request->design_faktor_id;

        $list = DesignFaktorKomponen::with('designfaktor');
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if($request->filled('design_faktor_id'))
        {
            $list->where('design_faktor_id',$df_id);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data = DesignFaktorKomponen::with('designfaktor')->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'nama' => 'required|unique:design_faktor_komponen,nama',
                'design_faktor_id' => 'required|uuid|exists:design_faktor,id',
            ],
            [
                'nama.required' => 'nama harus di isi',
                'nama.unique' => 'nama sudah digunakan',
                'design_faktor_id.required' => 'Harap pilih design faktor',
                'design_faktor_id.uuid' => 'Design faktor ID tidak valid',
                'design_faktor_id.exists' => 'Design faktor tidak terdaftar',
            ]
        );

        $data = new DesignFaktorKomponen();
        $data->nama = $request->nama;
        $data->design_faktor_id = $request->design_faktor_id;
        $data->deskripsi = $request->deskripsi;
        $data->save();

        return $this->successResponse();
    }

    public function edit(Request $request,$id)
    {
        $request->validate(
            [
                'nama' => 'required',
                'design_faktor_id' => 'required|uuid|exists:design_faktor,id',
            ],
            [
                'nama.required' => 'nama harus di isi',
                'design_faktor_id.required' => 'Harap pilih design faktor',
                'design_faktor_id.uuid' => 'Design faktor ID tidak valid',
                'design_faktor_id.exists' => 'Design faktor tidak terdaftar',
            ]
        );

        $data = DesignFaktorKomponen::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        $data->nama = $request->nama;
        $data->design_faktor_id = $request->design_faktor_id;
        $data->deskripsi = $request->deskripsi;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = DesignFaktorKomponen::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }
}
