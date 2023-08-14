<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoleUsers;
use App\Models\User;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use JsonResponse;

    public function login(Request $request)
    {
        $request->validate(
            [
                'username'=>'required',
                'password'=>'required'
            ],
            [
                'username.required'=>'Username harus di isi',
                'password.required' => 'Username harus di isi',
            ]
        );

        // $auth=User::select(['id','username','password'])->where('username',$request->username)->first();
        $auth = User::select('id', 'username', 'nama', 'email', 'divisi', 'posisi', 'status', 'internal','password')
            ->with([
                'roleaktif.role'
            ])
            ->where('username', $request->username)
            ->first();

        if(!$auth || !Hash::check($request->password, $auth->password)){
            return $this->errorResponse('Username atau password anda salah', 401);
        }
        if($auth->status == 'pending')
        {
            return $this->errorResponse('Akun anda belum aktifasi',401);
        }
        if ($auth->status == 'banned')
        {
            return $this->errorResponse('Akun anda sudah tidak aktif', 401);
        }

        if($auth->roleaktif == null)
        {
            return $this->errorResponse('Role belum terdaftar', 401);
        }

        // $account = User::select('id','username','nama','email','divisi','posisi','status','internal')
        //     ->with([
        //         'roleaktif.role'
        //     ])
        //     // ->where('username', $request->username)
        //     ->find($auth->id);

        $token=Auth::login($auth);
        $role_users=RoleUsers::with(['role'])->where('users_id',$auth->id)->get();
        $user=$auth;
        $user['roles']=$role_users;
        $data=[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ];
        return $this->successResponse($data);
    }
}
