<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\RoleUsers;
use App\Models\User;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
        $auth = User::select('id', 'username', 'nama', 'email', 'divisi', 'posisi', 'status', 'internal','password','organisasi_id','avatar')
            ->with([
                'organisasi',
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
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl'),
            'user' => $user
        ];

        return $this->successResponse($data);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'email'=>'required|email|exists:users,email',
            ],
            [
                'email.required'=>'Harap masukan email anda',
                'email.email' => 'Email tidak valid',
                'email.exists' => 'Email tidak terdaftar',
            ]
        );

        $user=User::where('email',$request->email)->first();
        $kode = strtoupper(Str::random(6));
        $otp=new Otp();
        $otp->kode=$kode;
        $otp->users_id=$user->id;
        $otp->expire_at = Carbon::now()->addMinute(1);
        $otp->aksi='reset-password';
        $otp->verify_by = 'email';
        $otp->save();

        return $this->successResponse($otp,'Email reset password terkirim');
    }

    public function detailByOtp($id)
    {

    }

    public function verifyResetPassword(Request $request)
    {
        $request->validate(
            [
                'otp'=>'required',
                'password' => ['required', Password::min(8), Password::min(8)->mixedCase(), Password::min(8)->numbers()],
            ],
            [
                'otp.required' => 'Kode OTP harus di isi',
                'password.required' => 'Password harus di isi',
                'password.min' => 'Password minimal 8 karakter',
            ]
        );

        $otp=Otp::where('kode',$request->otp)->first();
        $user=User::with(['otp'])->find($otp->users_id);
        return $this->successResponse($user);
    }
}
