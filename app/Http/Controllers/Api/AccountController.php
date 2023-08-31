<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserAssesment;
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
        $account= auth()->user();
        $user= $account;
        $user->assesment=$account->assesment;
        $user->organisasi = $account->organisasi;
        $user->roleaktif = $account->roleaktif;
        return $this->successResponse($account);
    }

    public function changeRole(Request $request)
    {
        $account = User::select('id', 'username', 'nama', 'email', 'divisi', 'posisi', 'status', 'internal')
            ->with(
                [
                    'role.role'
                ]
            )
            ->whereRelation('role', 'roles_id', '=', $request->id)
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

        return $this->successResponse($data);
    }

    public function changeAssesment(Request $request)
    {
        $assesment=Assesment::find($request->id);
        if(!$assesment)
        {
            return $this->errorResponse('Assesment tidak ditemukan',404);
        }

        $auth = User::select(
            'id',
            'username',
            'nama',
            'email',
            'divisi',
            'posisi',
            'status',
            'internal',
            'password',
            'organisasi_id',
            'avatar')
            ->with([
                    'organisasi',
                    'roleaktif.role',
                    'assesment'
                ])
            ->find(auth()->user()->id);

        $token = Auth::login($auth);
        $role_users = RoleUsers::with(['role'])->where('users_id', $auth->id)->get();
        $user = $auth;
        $user['roles'] = $role_users;
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl'),
            'user' => $user
        ];

        return $this->successResponse($data);
    }

    public function myAssesment()
    {
        $data=null;
        if(!$this->account->internal && isset($this->assesment->assesment_id))
        {
            $data=UserAssesment::with('assesment')
                ->where('assesment_id', $this->assesment->assesment_id)
                ->get();
        }

        return $this->successResponse($data);
    }

    public function setDefaultRole($id)
    {
        $data = RoleUsers::where('users_id', $this->account->id)->find($id);
        if (!$data) {
            return $this->errorResponse('Assesment tidak ditemukan', 404);
        }
        if (!$data->default) {
            RoleUsers::where('users_id', $this->account->id)->update([
                'default' => false
            ]);
            $data->default = true;
            $data->save();
        }

        return $this->successResponse();
    }

    public function setDefaultAssesment($id)
    {
        $data=UserAssesment::where('users_id',$this->account->id)->find($id);
        if(!$data)
        {
            return $this->errorResponse('Assesment tidak ditemukan',404);
        }
        if(!$data->default)
        {
            UserAssesment::where('users_id', $this->account->id)->update([
                'default' => false
            ]);
            $data->default = true;
            $data->save();
        }

        return $this->successResponse();
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

    public function edit(Request $request)
    {

        $data = User::find(Auth::user()->id);
        if (!$data)
        {
            $this->errorResponse('Data tidak ditemukan', 404);
        }

        if($request->filled('status'))
        {
            $validate['status']='in:active,banned,pending';
            $validate_msg['status.in'] = 'Status tidak valid (active,banned,pending)';
            $data->status = $request->status;
        }

        if ($request->filled('email'))
        {
            $validate['email']='email';
            $validate_msg['email.email'] = 'Email tidak valid';
            $data->email = $request->email;

            $_check_email = User::where('email', $request->email)
                ->where('id', '!=', Auth::user()->id)
                ->exists();

            if ($_check_email) {
                return $this->errorResponse('Email sudah digunakan', 400);
            }
        }

        if ($request->filled('username'))
        {
            $data->email = $request->email;
            $_check_username = User::where('username', $request->username)
                ->where('id', '!=', Auth::user()->id)
                ->exists();

            if ($_check_username)
            {
                return $this->errorResponse('Username sudah digunakan', 400);
            }
            $data->username = $request->username;
        }

        if($request->filled('nama'))
        {
            $data->nama = $request->nama;
        }

        if($request->hasFile('avatar'))
        {
            $validate['avatar'] = 'mimes:' . config('filesystems.validation.image.mimes') . '|max:' . config('filesystems.validation.image.size');
            $validate_msg['avatar.mimes']='Mimes invalid '.config('filesystems.validation.image.mimes');
            $validate_msg['avatar.max']='File maksimal '.config('filesystems.validation.image.size').' Kb';

            $path=config('filesystems.path.avatar');
            $avatar = $request->file('avatar');
            $filename = Auth::user()->username.'-'. $avatar->hashName();
            $avatar->storeAs($path, $filename);
            $data->avatar=CobitHelper::Media($filename,$path,$avatar);
        }

        if($request->filled('status') || $request->filled('email') || $request->hasFile('avatar'))
        {
            $request->validate($validate, $validate_msg);
        }

        $data->save();

        return $this->successResponse($data);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse();
    }
}
