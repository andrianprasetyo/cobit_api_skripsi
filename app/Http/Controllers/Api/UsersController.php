<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoleUsers;
use App\Models\User;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $list=User::with(['roles','roles.role']);
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
        $data=User::with(['roles','roles.role'])->find($id);
        if(!$data)
        {
            return $this->errorResponse('Pengguna tidak ditemukan',404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $validation['nama']='required';
        $msg_val['nama.required'] = 'Nama harus di isi';
        $validation['username'] = 'required';
        $msg_val['username.required'] = 'Username harus di isi';

        $validation['email'] = 'email|unique:users,email';
        $msg_val['email.email'] = 'Email tidak valid';
        $msg_val['email.unique'] = 'Email sudah digunakan';

        $validation['role_id']='required|uuid|exists:roles,id';
        $msg_val['role_id.required']='Role harus di isi';
        $msg_val['role_id.uuid'] = 'Role ID tidak valid';
        $msg_val['role_id.exists'] = 'Role tidak terdaftar';

        $username=Str::slug($request->username,'.');
        $_check_user=User::where('username',$username)->exists();
        if($_check_user)
        {
            return $this->errorResponse('Username sudah digunakan',400);
        }

        $request->validate($validation,$msg_val);

        $user = new User();
        $user->nama = $request->nama;
        $user->username = $username;
        $user->email = $request->email;
        $user->divisi = $request->divisi;
        $user->posisi = $request->posisi;
        $user->status='pending';
        $user->password='admin';
        $user->save();

        $role_user=new RoleUsers();
        $role_user->users_id=$user->id;
        $role_user->roles_id=$request->role_id;
        $role_user->default=true;
        $role_user->save();

        return $this->successResponse();
    }

    public function edit(Request $request,$id)
    {
        $request->validate(
            [
                'status'=>'required|in:active,banned,pending',
            ],
            [
                'status.required'=>'Status harus di isi',
                'status.in' => 'Status tidak valid (active,banned,pending)',
            ]
        );
        $data=User::find($id);
        if(!$data)
        {
            $this->errorResponse('Data tidak ditemukan',404);
        }

        $data->nama=$request->nama;
        $data->status = $request->status;
        $data->save();

        return $this->successResponse();
    }
}
