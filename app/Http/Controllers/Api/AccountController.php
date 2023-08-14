<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoleUsers;
use App\Models\User;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

    public function ubahPassword(Request $request)
    {
        $request->validate(
            [
                'old_password' => ['required'],
                'password' => ['required', 'confirmed', Password::min(8), Password::min(8)->mixedCase(), Password::min(8)->numbers()],
                'password_confirmation' => ['required'],
            ],
            [
                'old_password.required'=>'Password sebelumnya harus di isi',
                'password.required' => 'Password harus di isi',
                'password.confirmed' => 'Password tidak sama',
                'password_confirmation' => 'Konfirmasi password harus di isi',
            ]
        );

        $account = Auth::user();
        $user=User::find($account->id);
        $password = $request->password;
        $old_password = $request->old_password;
        if (!Hash::check($old_password, $user->password)) {
            return $this->errorResponse("Password yang anda masukan salah", 400);
        }

        $user->password = $password;
        $user->save();
        return $this->successResponse(null);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse();
    }
}
