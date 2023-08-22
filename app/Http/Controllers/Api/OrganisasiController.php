<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisasi;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        if (!$request->filled('limit')) {
            $limit = null;
            $page = null;
        }
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = Organisasi::with(['assesment']);
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = Organisasi::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }


    public function add(Request $request)
    {
        $request->validate(
            [
                'nama'=>'required|unique:organisasi,nama'
            ],
            [
                'nama.required'=>'Nama organisasi harus di isi',
                'nama.unique' => 'Nama organisasi sudah digunakan',
            ]
        );

        $organisasi=new Organisasi();
        $organisasi->nama=$request->nama;
        $organisasi->deskripsi = $request->deskripsi;
        $organisasi->save();

        return $this->successResponse();
    }

    public function edit(Request $request,$id)
    {
        $request->validate(
            [
                'nama' => 'required'
            ],
            [
                'nama.required' => 'Nama organisasi harus di isi'
            ]
        );
        $organisasi=Organisasi::find($id);
        if (!$organisasi) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $_check_exists=Organisasi::where('id','!=',$id)->where('nama',$request->nama)->exists();
        if($_check_exists)
        {
            return $this->errorResponse('Nama organisasi sudah digunakan',400);
        }

        $organisasi->nama = $request->nama;
        $organisasi->deskripsi = $request->deskripsi;
        $organisasi->save();

        return $this->successResponse();
    }

    public function deleteByID($id)
    {
        $data = Organisasi::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
        return $this->successResponse($data);
    }
}
