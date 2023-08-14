<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoleUsers;
use App\Models\User;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    use JsonResponse;
    public function me()
    {
        $account=auth()->user();
        return $this->successResponse($account);
    }

    public function changeRole()
    {
        $account = User::select('id', 'username', 'nama', 'email', 'divisi', 'posisi', 'status', 'internal')
            ->with(
                [
                    'role.role'
                ]
            )
            ->whereRelation('role', 'roles_id', '=', 'c5a34686-3824-11ee-8fa9-28d2440c0aa8')
            ->find(auth()->user()->id);

        $token = Auth::login($account);

        $role_users = RoleUsers::with(['role'])->where('users_id', $account->id)->get();

        $user = $account;
        $user['roles'] = $role_users;
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ];

        return $this->successResponse($user);
    }

    public function refresh()
    {
        $data = [
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];

        return $this->successResponse($data);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse();
    }
}
