<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganisasiDivisi;
use App\Models\OrganisasiDivisiMapDF;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $list = OrganisasiDivisi::with(['organisasi','jabatans']);
        if ($request->filled('organisasi_id')) {
            $list->where('organisasi_id', $organisasi_id);
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
        $data = OrganisasiDivisi::with(['organisasi','mapsdf.design_faktor'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {

        $validate['nama']='required';
        $validate_msg['nama.required'] = 'Nama divisi harus di isi';
        // if ($request->filled('is_specific_df') && $request->is_specific_df) {
        //     $validate['df'] = 'array';
        //     $validate_msg['df.array'] = 'DF harus berbentuk list';
        // }
        $request->validate($validate,$validate_msg);
        DB::beginTransaction();
        try {

            $divisi = new OrganisasiDivisi();
            $divisi->nama = $request->nama;
            $divisi->organisasi_id = $request->organisasi_id;
            $divisi->is_specific_df = $request->is_specific_df;
            $divisi->save();

            if ($request->filled('is_specific_df') && $request->is_specific_df) {
                if(!empty($request->df)){
                    foreach ($request->df as $item_df) {
                        $map=new OrganisasiDivisiMapDF();
                        $map->organisasi_divisi_id=$divisi->id;
                        $map->design_faktor_id = $item_df;
                        $map->save();
                    }
                }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(),$th->getCode());
        }
    }

    public function edit(Request $request, $id)
    {
        $validate['nama'] = 'required';
        $validate_msg['nama.required'] = 'Nama divisi harus di isi';
        if ($request->filled('is_specific_df') && $request->is_specific_df) {
            $validate['df'] = 'array';
            $validate_msg['df.array'] = 'DF harus berbentuk list';
        }
        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {
            $divisi = OrganisasiDivisi::find($id);
            if (!$divisi) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            $_check_exists = OrganisasiDivisi::where('id', '!=', $id)->where('nama', $request->nama)->exists();
            if ($_check_exists) {
                return $this->errorResponse('divisi organisasi sudah digunakan', 400);
            }

            $divisi->nama = $request->nama;
            $divisi->is_specific_df = $request->is_specific_df;
            if ($request->filled('organisasi_id')) {
                $divisi->organisasi_id = $request->organisasi_id;
            }
            $divisi->save();

            if ($request->filled('is_specific_df')) {
                if($request->is_specific_df && !empty($request->df)){
                    OrganisasiDivisiMapDF::where('organisasi_divisi_id',$id)->delete();
                    foreach ($request->df as $item_df) {
                        $map = new OrganisasiDivisiMapDF();
                        $map->organisasi_divisi_id = $id;
                        $map->design_faktor_id = $item_df;
                        $map->save();
                    }
                }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
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

    public function createMapDF(Request $request,$id)
    {
        $validate['df'] = 'array';
        $validate_msg['df.array'] = 'DF harus berbentuk list';
        $request->validate($validate, $validate_msg);
        $divisi = OrganisasiDivisi::find($id);
        if (!$divisi) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        DB::beginTransaction();
        try {

            OrganisasiDivisiMapDF::where('organisasi_divisi_id', $id)->delete();
            foreach ($request->df as $item_df) {
                $map = new OrganisasiDivisiMapDF();
                $map->organisasi_divisi_id = $id;
                $map->design_faktor_id = $item_df;
                $map->save();
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function deleteMapByID($id)
    {
        $data = OrganisasiDivisiMapDF::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
        return $this->successResponse($data);
    }
}
