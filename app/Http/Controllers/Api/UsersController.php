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
        $order = $request->get('order', 'created_at');
        $sort = $request->get('sort', 'desc');
        $search = $request->search;
        $status = $request->status;

        $list=User::query();
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
            $list->orWhere('username', 'ilike', '%' . $search . '%');
            $list->orWhere('email', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($order, $sort);

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
