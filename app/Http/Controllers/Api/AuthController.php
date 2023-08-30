<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\RoleAktifResource;
use App\Http\Resources\User\UserAssesmentsResource;
use App\Http\Resources\User\UserResource;
use App\Models\Otp;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserAssesment;
use App\Notifications\ResetPasswordNotif;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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
                'avatar'
                )
            ->with([
                'organisasi',
                'roleaktif.role',
                'assesment'
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

        // $auth->assesment_id=$auth->assesment != null?$auth->assesment->id : null;
        // $auth->organisasi_id = $auth->organisasi != null ? $auth->organisasi->id:null;

        $token=Auth::login($auth);
        $role_users=RoleUsers::with(['role'])->where('users_id',$auth->id)->get();
        $user = $auth;
        if(!$auth->internal){
            $assesment=UserAssesment::with('assesment')->where('users_id',$auth->id)->get();
            $user['assesments'] = UserAssesmentsResource::collection($assesment);
        }

        $user['roles'] = $role_users;
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

        $exp=3;
        $kode = strtoupper(Str::random(6));
        $otp=new Otp();
        $otp->kode=$kode;
        $otp->users_id=$user->id;
        $otp->expire_at = Carbon::now()->addMinute($exp);
        $otp->aksi='reset-password';
        $otp->verify_by = 'email';
        $otp->token=Str::random(50);
        $otp->save();

        $user->otp=$otp;
        // $user->notify(new ResetPasswordNotif($otp));
        Notification::send($user, new ResetPasswordNotif());

        return $this->successResponse(null,'Email reset password terkirim, akan kadaluarsa dalam '.$exp.' menit');
    }

    public function detailVerifyByToken(Request $request)
    {
        $token=$request->token;
        $otp=Otp::where('token',$token)->first();
        if(!$token)
        {
            return $this->errorResponse('Invalid token',400);
        }
        if(Carbon::now()->gte(Carbon::parse($otp->expire_at)))
        {
            return $this->errorResponse('Token Expired',400);
        }

        return $this->successResponse();
    }

    public function _checkKodeByToken(Request $request)
    {
        $request->validate(
            [
                'kode' => 'required',
                'token'=>'required',
            ],
            [
                'kode.required'=>'Kode OTP harus di isi',
                'token.required'=>'Token harus di isi',
            ]
        );

        $otp=Otp::where('token',$request->token)->first();
        if($request->kode != $otp->kode)
        {
            return $this->errorResponse('Kode OTP yang anda masukan salah',400);
        }


        return $this->successResponse();
    }

    public function verifyResetPassword(Request $request)
    {
        $request->validate(
            [
                // 'otp'=>'required',
                'token' => 'required',
                'password' => ['required', Password::min(8), Password::min(8)->mixedCase(), Password::min(8)->numbers()],
                'password_confirmation' => ['required'],
            ],
            [
                // 'otp.required' => 'Kode OTP harus di isi',
                'token.required' => 'Token harus di isi',
                'password.required' => 'Password harus di isi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Password tidak sama',
                'password_confirmation' => 'Konfirmasi password harus di isi',
            ]
        );

        $otp=Otp::where('token',$request->token)->first();
        if(!$otp)
        {
            return $this->errorResponse('Token tidak valid/tersedia',400);
        }

        $user=User::find($otp->users_id);
        $user->password=$request->password;
        $user->save();

        return $this->successResponse(null,'Password berhasil diubah');
    }

    public function tokenVerify(Request $request)
    {
        // $_token_check=User::where('token',$request->token)->first();
        $_token_check = User::select(
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
            'avatar',
            'token'
        )
            ->with([
                'organisasi',
                // 'roleaktif.role',
                'assesment'
            ])
            ->where('token', $request->token)
            ->first();

        if(!$_token_check)
        {
            return $this->errorResponse('Token tidak valid',404);
        }

        return $this->successResponse($_token_check);
    }

    public function userTokenVerify(Request $request)
    {

        $request->validate(
            [
                // 'otp'=>'required',
                // 'token' => 'required',
                'id'=>'required|exists:users,id',
                'password' => ['required', Password::min(8), Password::min(8)->mixedCase(), Password::min(8)->numbers()],
                'password_confirmation' => ['required'],
            ],
            [
                // 'otp.required' => 'Kode OTP harus di isi',
                // 'token.required' => 'Token harus di isi',
                'id.required'=>'Users ID harus di isi',
                'id.exists' => 'Users ID tidak terdaftar',
                'password.required' => 'Password harus di isi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Password tidak sama',
                'password_confirmation' => 'Konfirmasi password harus di isi',
            ]
        );
        $user = User::find($request->id);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        if($user->status == 'active')
        {
            return $this->errorResponse('User sudah melakukan aktifasi', 400);
        }

        if ($user->status == 'banned') {
            return $this->errorResponse('User sudah di blokir', 400);
        }

        $user->status='active';
        $user->token=null;
        $user->email_verified_at=Carbon::now();
        $user->save();

        return $this->successResponse();
    }
}
