<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisasi\JabatanDivisiRequest;
use App\Models\OrganisasiDivisiJabatan;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class OrganisasiJabatanController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;
        $organisasi_divisi_id = $request->organisasi_divisi_id;

        $list = OrganisasiDivisiJabatan::with(['divisi']);
        if($request->filled('organisasi_divisi_id'))
        {
            $list->where('organisasi_divisi_id',$organisasi_divisi_id);
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
        $data = OrganisasiDivisiJabatan::with('divisi')->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        // $request->validated();

        $jabatan = new OrganisasiDivisiJabatan();
        $jabatan->nama = $request->nama;
        $jabatan->organisasi_divisi_id = $request->organisasi_divisi_id;
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
                'nama.required' => 'Nama Jabatan harus di isi'
            ]
        );
        $jabatan = OrganisasiDivisiJabatan::find($id);
        if (!$jabatan) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $_check_exists = OrganisasiDivisiJabatan::where('id', '!=', $id)->where('nama', $request->nama)->exists();
        if ($_check_exists) {
            return $this->errorResponse('Jabatan organisasi sudah digunakan', 400);
        }

        $jabatan->nama = $request->nama;
        $jabatan->organisasi_divisi_id = $request->organisasi_divisi_id;
        $jabatan->save();

        return $this->successResponse();
    }

    public function deleteByID($id)
    {
        $data = OrganisasiDivisiJabatan::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
        return $this->successResponse($data);
    }
}
