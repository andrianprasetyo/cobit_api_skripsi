<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assesment\AddPICRequest;
use App\Http\Resources\AssesmentResource;
use App\Imports\RespondenImport;
use App\Models\Assesment;
use App\Models\AssesmentHasil;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\Organisasi;
use App\Models\Quisioner;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserAssesment;
use App\Notifications\InviteRespondenNotif;
use App\Notifications\InviteUserNotif;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AsessmentController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = Assesment::with(['organisasi','pic']);

        if($this->assesment != null)
        {
            $account=$this->account;
            $list->whereIn('id',function($q) use($account){
                $q->select('assesment_id')
                    ->from('users_assesment')
                    ->where('users_id',$account->id);
            });
        }
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if($request->filled('status'))
        {
            $list->where('status',$request->status);
        }

        if ($request->filled('organisasi_id')) {
            $list->where('organisasi_id', $request->organisasi_id);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page,AssesmentResource::class);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data=Assesment::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse(new AssesmentResource($data));
    }

    public function add(Request $request)
    {

        $validate['asessment']='required';
        // $validate['tahun'] = 'required|date_format:Y-m';

        $validate['start_date'] = 'required|date_format:Y-m-d|before:end_date';
        $validate['end_date'] = 'required|date_format:Y-m-d|after:start_date';

        $validate_msg['start_date.required']='Tanggal mulai harus di isi';
        $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date.before'] = 'Tanggal mulai harus sebelum tanggal selesai';
        $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
        $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date.before'] = 'Tanggal selesai harus sesudah tanggal mulai';

        $validate_msg['asessment.required']='Nama assesment harus di isi';
        // $validate_msg['tahun.required'] = 'Tahun assesment harus di isi';
        // $validate_msg['tahun.date_format'] = 'Tahun assesment tidak valid (Y-m)';

        $validate['pic_nama']='required';
        $validate['pic_email'] = 'required|email|unique:users,email';
        $validate_msg['pic_nama.required']='Nama PIC harus di isi';
        $validate_msg['pic_email.required'] = 'Email PIC harus di isi';
        $validate_msg['pic_email.email'] = 'Email PIC tidak valid';
        $validate_msg['pic_email.unique'] = 'Email PIC sudah digunakan';

        if ($request->filled('organisasi_id')) {
            $validate['organisasi_id'] = 'uuid|exists:organisasi,id';
            $validate_msg['organisasi_id.uuid']='Organisasi ID tidak valid';
            $validate_msg['organisasi_id.exists'] = 'Organisasi tidak terdaftar';
        }else{
            $validate['organisasi_nama']='required|unique:organisasi,nama';
            $validate_msg['organisasi_nama.required']='Nama organisasi harus di isi';
            $validate_msg['organisasi_nama.unique'] = 'Nama organisasi sudah digunakan';
        }

        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {

            $organisasi_id = $request->organisasi_id;
            if ($request->filled('organisasi_nama'))
            {
                $organisasi = new Organisasi();
                $organisasi->nama = $request->organisasi_nama;
                $organisasi->deskripsi = $request->organisasi_deskripsi;
                $organisasi->save();

                $organisasi_id=$organisasi->id;
            }

            // $verify_code=Str::random(50);
            // $user_ass=new AssessmentUsers();
            // $user_ass->assesment_id=$assesment->id;
            // $user_ass->nama=$request->pic_nama;
            // $user_ass->email = $request->pic_email;
            // $user_ass->divisi = $request->pic_divisi;
            // $user_ass->jabatan = $request->pic_jabatan;
            // $user_ass->code=$verify_code;
            // $user_ass->status='invited';
            // $user_ass->save();

            $role=Roles::where('code','eksternal')->first();
            if(!$role)
            {
                return $this->errorResponse('Role Eksternal tidak tersedia',404);
            }

            $_token=Str::random(50);
            $user=new User();
            $user->nama=$request->pic_nama;
            $user->divisi = $request->pic_divisi;
            $user->posisi = $request->pic_jabatan;
            $user->email = $request->pic_email;
            $user->status='pending';
            $user->internal=false;
            $user->organisasi_id=$organisasi_id;
            $user->token=$_token;
            $user->password= $_token;
            $user->username=Str::slug($request->pic_nama, '.');
            $user->save();

            $role_user=new RoleUsers();
            $role_user->users_id = $user->id;
            $role_user->roles_id=$role->id;
            $role_user->default=true;
            $role_user->save();

            $assesment = new Assesment();
            $assesment->nama = $request->asessment;
            $assesment->deskripsi = $request->deskripsi;
            $assesment->organisasi_id = $organisasi_id;
            $assesment->start_date = $request->start_date;
            $assesment->end_date = $request->end_date;
            $assesment->status = 'ongoing';
            $assesment->users_id=$user->id;
            $assesment->save();

            $user_ass=new UserAssesment();
            $user_ass->users_id=$user->id;
            $user_ass->assesment_id=$assesment->id;
            $user_ass->default=true;
            $user_ass->save();

            // $user->assesment=$user_ass;
            // $user->notify(new InviteUserNotif());
            Notification::send($user, new InviteUserNotif($user));

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function edit(Request $request,$id)
    {
        $validate['start_date'] = 'required|date_format:Y-m-d|before:end_date';
        $validate['end_date'] = 'required|date_format:Y-m-d|after:start_date';

        $validate_msg['start_date.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date.before'] = 'Tanggal mulai harus sebelum tanggal selesai';
        $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
        $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date.before'] = 'Tanggal selesai harus sesudah tanggal mulai';
        $request->validate($validate, $validate_msg);

        $data = Assesment::with(['organisasi','pic'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->nama=$request->nama;
        $data->deskripsi = $request->deskripsi;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->save();

        return $this->successResponse();
    }
    public function setStatus(Request $request,$id)
    {
        $request->validate(
            [
                'status'=>'required|in:ongoing,completed',
            ],
            [
                'status.required'=>'Status harus di isi',
                'status.in' => 'Status tidak valid (ongoing|completed)',
            ]
        );

        $data=Assesment::find($id);
        $data->status=$request->status;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = Assesment::find($id);
        if (!$data)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();

        return $this->successResponse();
    }

    public function inviteRespondent(Request $request)
    {

        DB::beginTransaction();
        try {

            $validate['id'] = 'required|exists:assesment,id';
            $validate_msg['id.required'] = 'assesment ID harus di isi';
            $validate_msg['id.exists'] = 'assesment ID tidak terdaftar';

            $validate['email'] = [
                'required',
                'array',
                function($attribute,$value,$fail) use($request){
                    $_chekc_exists_mail = AssessmentUsers::select('email')
                        ->where('assesment_id', $request->id)
                        ->whereIn('email', $value)
                        ->get();

                    if (!$_chekc_exists_mail->isEmpty())
                    {
                        $mail=[];
                        foreach ($_chekc_exists_mail as $_item_mail) {
                            $mail[]=$_item_mail->email;
                        }
                        $fail('Terdapat email yang sudah terdaftar pada assesment yang sama ('. implode(',', $mail).')');
                    }
                }
            ];
            $validate['email.*'] = 'required|email';

            $validate_msg['email.required'] = 'Email harus di isi';
            $validate_msg['email.array'] = 'Email harus dalam bentuk array';
            $validate_msg['email.*.required'] = 'Email harus di isi';
            $validate_msg['email.*.email'] = 'Email tidak valid';

            $request->validate($validate, $validate_msg);

            // return $this->successResponse();
            // $_chekc_exists_mail=AssessmentUsers::where('assesment_id',$request->id)->whereIn('email',$request->email)->exists();
            // if($_chekc_exists_mail)
            // {
            //     return $this->errorResponse('Terdapat email yang sudah terdaftar pada assesment yang sama',400);
            // }

            $assesment = Assesment::with('organisasi')->find($request->id);
            if (!$assesment) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            if($assesment->status == 'completed')
            {
                return $this->errorResponse('Assesment sudah dilaksanakan/Completed',400);
            }

            // $_exp_date=Carbon::parse($assesment->tahun);
            // if(Carbon::now()->gte($_exp_date))
            // {
            //     return $this->errorResponse('Assesment sudah lewat batas tahun ('.$_exp_date.')');
            // }
            $organisasi=$assesment->organisasi;
            // $quisioner=Quisioner::where('aktif',true)->first();
            // foreach ($request->responden as $_item_responden)
            // {
            //     $responden=new AssessmentUsers();
            //     // $responden->nama=$_item_responden['nama'];
            //     $responden->email = $_item_responden['email'];
            //     // $responden->divisi = $_item_responden['divisi'];
            //     // $responden->jabatan = $_item_responden['jabatan'];
            //     // $responden->assesment_id = $assesment->id;
            //     // $responden->status = 'active';
            //     $responden->code = Str::random(50);
            //     $responden->save();

            //     // $quisioner_responden=new AssessmentQuisioner();
            //     // $quisioner_responden->assesment_id=$assesment->id;
            //     // $quisioner_responden->quisioner_id = $quisioner->id;
            //     // $quisioner_responden->organisasi_id = $quisioner->organisasi->id;
            //     // $quisioner_responden->allow=true;
            //     // $quisioner_responden->save();
            //     // $responden->notify(new InviteRespondenNotif($assesment));
            //     // Queue::push(new InviteRespondenNotif($assesment));
            //     Notification::send($responden,new InviteRespondenNotif($organisasi));
            //     // Notification::route('mail', $responden->email)->notify(new InviteRespondenNotif($organisasi));
            // }

            foreach ($request->email as $_item_email)
            {
                $responden = new AssessmentUsers();
                $responden->email = $_item_email;
                $responden->assesment_id = $assesment->id;
                $responden->status = 'diundang';
                $responden->code = Str::random(50);
                $responden->save();
                Notification::send($responden, new InviteRespondenNotif($organisasi));
            }
            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function inviteRespondentByExcel(Request $request)
    {

        $request->validate(
            [
                'id'=>'required|uuid|exists:assesment,id',
                'file'=> 'required|mimes:'.config('filesystems.validation.docs.excel.mimes').'|max:'. config('filesystems.validation.docs.excel.size')
            ],
            [
                'id.required' => 'Assesment ID harus di isi',
                'id.uuid' => 'Assesment ID tidak valid',
                'id.exists' => 'Assesment ID tidak terdaftar',
                'file.required'=>'File harus di isi',
                'file.mimes' => 'File tidak valid ('. config('filesystems.validation.docs.excel.mimes').')',
                'file.max'=>'Maksimal file (' . config('filesystems.validation.docs.excel.size') . ')',
            ]
        );

        $file = $request->file('file');

        $assesment = Assesment::find($request->id);
        if ($assesment->status == 'completed') {
            return $this->errorResponse('Assesment sudah dilaksanakan/Completed', 400);
        }

        // $_exp_date = Carbon::parse($assesment->tahun);
        // if (Carbon::now()->gte($_exp_date)) {
        //     return $this->errorResponse('Assesment sudah lewat batas tahun (' . $_exp_date . ')');
        // }

        try {

            Excel::import(new RespondenImport($request->id), $file);
            return $this->successResponse();
        } catch (\Exception $e) {
            // DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function reinviteResponden($id)
    {
        $responden=AssessmentUsers::with(['assesment.organisasi'])->find($id);
        if(!$responden)
        {
            return $this->errorResponse('Responden tidak ditemukan',404);
        }
        if($responden->is_proses == 'done')
        {
            return $this->errorResponse('Responden sudah melakukan Kuisioner',400);
        }

        $organisasi = $responden->assesment->organisasi;
        Notification::send($responden, new InviteRespondenNotif($organisasi,'Kirim ulang Undangan Kuisioner Responden'));

        return $this->successResponse();
    }

    public function addPIC(AddPICRequest $request)
    {

        $request->validated();

        $users = $request->user_id;
        $assesment_id = $request->assesment_id;
        $organisasi_id = $request->organisasi_id;
        $account = auth()->user();

        if (isset($account->organisasi->id)) {
            $organisasi_id = $account->organisasi->id;
        }

        $_check_assesment = Assesment::where('id', $assesment_id)
            ->where('organisasi_id', $organisasi_id)
            ->exists();

        if (!$_check_assesment) {
            $_ass = Assesment::find($assesment_id);
            $_organisasi = Organisasi::find($organisasi_id);
            return $this->errorResponse('Assesment ' . $_ass->nama . ' tidak terdaftar pada organisasi ' . $_organisasi->nama);
        }

        $role = Roles::where('code', 'eksternal')->first();
        if (!$role) {
            return $this->errorResponse('Role Eksternal tidak tersedia', 404);
        }

        DB::beginTransaction();
        try {
            foreach ($users as $_item_user) {
                $user_id = isset($_item_user['id']) ? $_item_user['id'] : null;

                if (isset($_item_user['email'])) {
                    $email = $_item_user['email'];
                    $_token = Str::random(50);
                    $user = new User();
                    $user->email = $email;
                    $user->status = 'pending';
                    $user->internal = false;
                    $user->organisasi_id = $organisasi_id;
                    $user->token = $_token;
                    $user->password = $_token;
                    $user->username = null;
                    $user->save();

                    $user_id = $user->id;

                    $role_user = new RoleUsers();
                    $role_user->users_id = $user_id;
                    $role_user->roles_id = $role->id;
                    $role_user->default = true;
                    $role_user->save();

                    Notification::send($user, new InviteUserNotif($user));
                }

                $user_ass = new UserAssesment();
                $user_ass->users_id = $user_id;
                $user_ass->assesment_id = $assesment_id;
                $user_ass->default = false;
                $user_ass->save();

                // if (isset($_item_user['id'])) {
                //     // notif invite assesment users exists
                // }
            }

            DB::commit();
            $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function editPIC(Request $request,$id)
    {
        $user=User::find($id);
        if(!$user)
        {
            return $this->errorResponse('User tidak ditemukan',404);
        }

        if($user->status !='pending')
        {
            return $this->errorResponse('User sudah melakukan verifikasi',400);
        }

        $_mail_check=User::where('email',$request->pic_email)->where('id','!=',$user->id)->exists();
        if($_mail_check)
        {
            return $this->errorResponse('Email .'.$request->pic_email.' sudah digunakan',400);
        }
        $_token = Str::random(50);
        $user->nama = $request->pic_nama;
        $user->divisi = $request->pic_divisi;
        $user->posisi = $request->pic_jabatan;
        $user->email = $request->pic_email;
        $user->token = $_token;
        $user->password = $_token;
        $user->save();

        Notification::send($user, new InviteUserNotif($user));
        return $this->successResponse();
    }

    public function reAktifasi(Request $request,$id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        if ($user->status != 'pending') {
            return $this->errorResponse('User sudah melakukan verifikasi', 400);
        }

        $_token = Str::random(50);
        $user->token = $_token;
        $user->save();

        Notification::send($user, new InviteUserNotif($user));
        return $this->successResponse();
    }
}
