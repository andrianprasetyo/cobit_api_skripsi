<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
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

}
