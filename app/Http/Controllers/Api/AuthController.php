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
                'username'=>'Username harus di isi',
                'password' => 'Username harus di isi',
            ]
        );

        $auth=User::select(['username','password'])->where('username',$request->username)->first();

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

        $account = User::select('id','username','nama','email','divisi','posisi','status','internal')
            ->with([
                'role.role'
            ])
            ->where('username', $request->username)
            ->first();

        $token=Auth::login($account);

        $role_users=RoleUsers::with(['role'])->where('users_id',$account->id)->get();

        $user=$account;
        $user['roles']=$role_users;
        $data=[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ];
        return $this->successResponse($account);
    }

    public function refresh(){
        $data=[
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];

        return $this->successResponse($data);
    }

    public function refresh(){
        $data=[
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];

        return $this->successResponse($data);
    }
}
