<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit=$request->get('limit',10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;
        $status = $request->status;
        $role = $request->role;

        $list=User::with(['roles']);
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
            $list->orWhere('username', 'ilike', '%' . $search . '%');
            $list->orWhere('email', 'ilike', '%' . $search . '%');
        }
        if($request->filled('status'))
        {
            $list->where('status',$status);
        }

        if ($request->filled('organisasi_id'))
        {
            $list->where('organisasi_id', $request->organisasi_id);
        }

        if ($request->filled('role'))
        {
            $list->whereRelation('roles','roles_id',$role);
        }

        $list->orderBy($sortBy, $sortType);

        $data=$this->paging($list,$limit,$page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data=User::find($id);
        if(!$data)
        {
            return $this->errorResponse('Pengguna tidak ditemukan',404);
        }

        return $this->successResponse($data);
    }
}
