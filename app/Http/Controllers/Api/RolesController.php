<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    use JsonResponse;
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        if (!$request->filled('limit'))
        {
            $limit=null;
            $page=null;
        }
        $sortBy = $request->get('sortBy', 'nama');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;

        $list = Roles::orderBy($sortBy, $sortType);
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }


        $data = $this->paging($list,$limit,$page);
        return $this->successResponse($data);
    }

    public function addRole(Request $request)
    {
        $request->validate(
            [
                'nama'=>'required|unique:roles',
            ],
            [
                'nama.required'=>'Nama role harus di isi',
                'nama.unique' => 'Nama role sudah di gunakan',
            ]
        );

        $role=new Roles();
        $role->nama=$request->nama;
        $role->code=Str::slug($request->nama, '.');
        $role->status=true;
        $role->save();

        return $this->successResponse();
    }
}
