<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganisasiDivisi;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class OrganisasiDivisiController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;
        $organisasi_id = $request->organisasi_id;
        $jenis = $request->jenis;

        $list = OrganisasiDivisi::with(['organisasi']);
        if ($request->filled('organisasi_id')) {
            $list->where('organisasi_id', $organisasi_id);
        }
        if ($request->filled('jenis')) {
            $list->where('jenis', $jenis);
        }
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = OrganisasiDivisi::with('organisasi')->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $request->validated();

        $jabatan = new OrganisasiDivisi();
        $jabatan->nama = $request->nama;
        $jabatan->jenis = $request->jenis;
        $jabatan->organisasi_id = $request->organisasi_id;
        $jabatan->save();

        return $this->successResponse();
    }

    public function edit(Request $request, $id)
    {
        $request->validate(
            [
                'nama' => 'required'
            ],
            [
                'nama.required' => 'Nama jabatan/divisi harus di isi'
            ]
        );
        $jabatan = OrganisasiDivisi::find($id);
        if (!$jabatan) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $_check_exists = OrganisasiDivisi::where('id', '!=', $id)->where('nama', $request->nama)->exists();
        if ($_check_exists) {
            return $this->errorResponse('Jabatan/Divisi organisasi sudah digunakan', 400);
        }

        $jabatan->nama = $request->nama;
        $jabatan->jenis = $request->jenis;
        $jabatan->organisasi_id = $request->organisasi_id;
        $jabatan->save();

        return $this->successResponse();
    }

    public function deleteByID($id)
    {
        $data = OrganisasiDivisi::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
        return $this->successResponse($data);
    }
}
