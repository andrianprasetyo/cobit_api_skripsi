<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
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

        $data = $this->paging($list,$limit,$page, RoleResource::class);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $role = Roles::find($id);
        if (!$role) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        $data=new RoleResource($role);
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
        $role->aktif=true;
        $role->deskripsi = $request->deskripsi;
        $role->save();

        return $this->successResponse();
    }

    public function editRole(Request $request,$id)
    {
        $role=Roles::find($id);
        if(!$role)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        if($request->filled('aktif'))
        {
            $role->aktif=$request->aktif;
        }
        if($request->filled('nama'))
        {
            $role->nama = $request->nama;
        }
        $role->deskripsi = $request->deskripsi;
        $role->save();
        return $this->successResponse();
    }

    public function deleteRole($id)
    {
        $role = Roles::withCount(['usersrole as total_user'])->find($id);
        if (!$role) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        if($role->total_user > 0)
        {
            return $this->errorResponse('Role '.$role->nama.' sedang digunakan',400);
        }

        $role->delete();
        return $this->successResponse();
    }
}
