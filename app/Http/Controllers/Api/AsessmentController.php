<?php

namespace App\Http\Controllers\Api;

use App\Exports\AnalisaGapExport;
use App\Exports\AssesmentOfiByDomainExport;
use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assesment\AddPICRequest;
use App\Http\Resources\Assesment\AssesmentDFInputSectionResource;
use App\Http\Resources\Assesment\AssesmentGapReportResource;
use App\Http\Resources\AssesmentResource;
use App\Imports\RespondenImport;
use App\Models\Assesment;
use App\Models\AssesmentCanvas;
use App\Models\AssesmentHasil;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentOfi;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use App\Models\Domain;
use App\Models\Organisasi;
use App\Models\OrganisasiDivisi;
use App\Models\OrganisasiDivisiJabatan;
use App\Models\Quisioner;
use App\Models\QusisionerHasilAvg;
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

        $list = Assesment::with(['organisasi','pic'])->expire();

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
        $data=Assesment::with('pic.divisi','pic.jabatan')->find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse(new AssesmentResource($data));
    }

    public function add(Request $request)
    {
        $today=Carbon::today()->format('Y-m-d');
        $validate['asessment']='required';
        // $validate['tahun'] = 'required|date_format:Y-m';

        $validate['start_date'] = 'required|date_format:Y-m-d|after_or_equal:'.$today;
        $validate['end_date'] = 'required|date_format:Y-m-d|after:start_date';

        $validate_msg['start_date.required']='Tanggal mulai harus di isi';
        $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date.after_or_equal'] = 'Tanggal mulai harus setelah tanggal sekarang';
        $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
        $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date.after'] = 'Tanggal selesai harus sesudah tanggal mulai';

        $validate_msg['asessment.required']='Nama assesment harus di isi';
        // $validate_msg['tahun.required'] = 'Tahun assesment harus di isi';
        // $validate_msg['tahun.date_format'] = 'Tahun assesment tidak valid (Y-m)';

        $validate['pic_nama']='required';
        $validate['pic_email'] = 'required|email';
        $validate_msg['pic_nama.required']='Nama PIC harus di isi';
        $validate_msg['pic_email.required'] = 'Email PIC harus di isi';
        $validate_msg['pic_email.email'] = 'Email PIC tidak valid';

        // $validate_msg['pic_email.unique'] = 'Email PIC sudah digunakan';
        $validate['pic_expire_at'] = 'required|date_format:Y-m-d|after:today';
        $validate_msg['pic_expire_at.required'] = 'Tanggal kadaluarsa PIC harus di isi';
        $validate_msg['pic_expire_at.date_format'] = 'Tanggal kadaluarsa PIC tidak valid (Y-m-d)';
        $validate_msg['pic_expire_at.after'] = 'Tanggal kadaluarsa harus setelah hari ini';

        $validate['start_date_quisioner'] = 'required|date_format:Y-m-d|after_or_equal:'.$today;
        $validate_msg['start_date_quisioner.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['start_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date_quisioner.after_or_equal'] = 'Tanggal mulai quisioner harus setelah tanggal mulai assesment';

        $validate['end_date_quisioner'] = 'required|date_format:Y-m-d|before:end_date';
        $validate_msg['end_date_quisioner.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['end_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date_quisioner.before'] = 'Tanggal selesai quisioner harus sebelum tanggal selesai assesment';

        if ($request->filled('organisasi_id')) {
            $validate['organisasi_id'] = 'uuid|exists:organisasi,id';
            $validate_msg['organisasi_id.uuid']='Organisasi ID tidak valid';
            $validate_msg['organisasi_id.exists'] = 'Organisasi tidak terdaftar';

            $validate['pic_divisi_id'] = 'uuid|exists:organisasi_divisi,id';
            $validate_msg['pic_divisi_id.uuid'] = 'Jabatan ID tidak valid';
            $validate_msg['pic_divisi_id.exists'] = 'Jabatan tidak terdaftar';

            $validate['pic_jabatan_id'] = 'uuid|exists:organisasi_divisi_jabatan,id';
            $validate_msg['pic_jabatan_id.uuid'] = 'Divisi ID tidak valid';
            $validate_msg['pic_jabatan_id.exists'] = 'Divisi tidak terdaftar';

        }else{
            $validate['organisasi_nama']='required|unique:organisasi,nama';
            $validate_msg['organisasi_nama.required']='Nama organisasi harus di isi';
            $validate_msg['organisasi_nama.unique'] = 'Nama organisasi sudah digunakan';

            $validate['pic_jabatan'] = 'required|string';
            $validate['pic_divisi'] = 'required|string';
        }

        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {

            $pic_email=$request->pic_email;
            $organisasi_id = $request->organisasi_id;
            $pic_jabatan_id = $request->pic_jabatan_id;
            $pic_divisi_id = $request->pic_divisi_id;

            if ($request->filled('organisasi_nama')) {
                $organisasi = new Organisasi();
                $organisasi->nama = $request->organisasi_nama;
                $organisasi->deskripsi = $request->organisasi_deskripsi;
                $organisasi->save();

                $organisasi_id = $organisasi->id;

                if ($request->filled('pic_divisi')) {
                    $divisi = new OrganisasiDivisi();
                    $divisi->nama = $request->pic_divisi;
                    $divisi->organisasi_id = $organisasi_id;
                    $divisi->save();
                    $pic_divisi_id = $divisi->id;
                }

                if ($request->filled('pic_jabatan')) {
                    $jabatan = new OrganisasiDivisiJabatan();
                    $jabatan->nama = $request->pic_jabatan;
                    $jabatan->organisasi_divisi_id = $pic_divisi_id;
                    $jabatan->save();

                    $pic_jabatan_id = $jabatan->id;
                }
            }
            if(!$this->account->internal){
                $organisasi_id=$this->account->organisasi->id;
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

            $_check_mail_exists = User::where('email', $pic_email)->first();
            $user_id=null;
            // if (!$this->account->internal) {
            //     $user_id = $this->account->id;
            // }
            $default_ass=false;
            if(!$_check_mail_exists)
            {
                $_token=Str::random(50);
                $user=new User();
                $user->nama=$request->pic_nama;
                $user->divisi = $request->pic_divisi;
                $user->posisi = $request->pic_jabatan;
                $user->email = $request->pic_email;
                $user->status='pending';
                $user->internal=false;
                $user->organisasi_id=$organisasi_id;
                $user->jabatan_id = $pic_jabatan_id;
                $user->divisi_id=$pic_divisi_id;
                $user->token=$_token;
                $user->password= $_token;
                $user->username=Str::slug($request->pic_nama, '.');
                $user->save();

                $role_user=new RoleUsers();
                $role_user->users_id = $user->id;
                $role_user->roles_id=$role->id;
                $role_user->default=true;
                $role_user->save();

                // if($this->account->internal){
                //     $user_id=$user->id;
                // }
                $user_id = $user->id;
                $default_ass=true;
            }else{
                $user=$_check_mail_exists;
                $user_id=$_check_mail_exists->id;
                $default_ass = false;
            }

            // $user_id = $this->account->id;
            // if ($this->account->internal) {
            //     $user_id = $user->id;
            // }else{
            //     $user_id = $this->account->id;
            // }

            $assesment = new Assesment();
            $assesment->nama = $request->asessment;
            $assesment->deskripsi = $request->deskripsi;
            $assesment->organisasi_id = $organisasi_id;
            $assesment->start_date = $request->start_date;
            $assesment->end_date = $request->end_date;
            $assesment->start_date_quisioner = $request->start_date_quisioner;
            $assesment->end_date_quisioner=$request->end_date_quisioner;
            $assesment->status = 'ongoing';
            $assesment->users_id=$user_id;
            $assesment->minimum_target=$request->filled('minimum_target')?$request->minimum_target:3;
            $assesment->save();

            $user_ass = new UserAssesment();
            $user_ass->users_id = $user_id;
            $user_ass->assesment_id = $assesment->id;
            $user_ass->default = $default_ass;
            $user_ass->expire_at = $request->pic_expire_at;
            $user_ass->save();

            // $user->assesment=$user_ass;
            // $user->notify(new InviteUserNotif());
            if (!$_check_mail_exists)
            {
                Notification::send($user, new InviteUserNotif($user));
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function edit(Request $request,$id)
    {
        $today=Carbon::today()->format('Y-m-d');
        if($request->filled('start_date')){
            $validate['start_date'] = 'required|date_format:Y-m-d';
            $validate_msg['start_date.required'] = 'Tanggal mulai harus di isi';
            $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['start_date.after_or_equal'] = 'Tanggal mulai harus setelah tanggal sekarang';
        }

        if($request->filled('end_date')){
            $validate['end_date'] = 'required|date_format:Y-m-d';

            $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
            $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['end_date.after'] = 'Tanggal selesai assesment harus setelah tanggal mulai assesment';
        }

        if($request->filled('start_date_quisioner')){
            $validate['start_date_quisioner'] = 'required|date_format:Y-m-d';
            $validate_msg['start_date_quisioner.required'] = 'Tanggal mulai harus di isi';
            $validate_msg['start_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['start_date_quisioner.after'] = 'Tanggal mulai quisioner harus setelah tanggal mulai assesment';
        }

        if ($request->filled('end_date_quisioner')) {
            $validate['end_date_quisioner'] = 'required|date_format:Y-m-d';
            $validate_msg['end_date_quisioner.required'] = 'Tanggal mulai harus di isi';
            $validate_msg['end_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['end_date_quisioner.before'] = 'Tanggal mulai quisioner harus sebelum tanggal selesai assesment';
        }

        $request->validate($validate, $validate_msg);

        $data = Assesment::with(['organisasi','pic'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->nama=$request->nama;
        $data->deskripsi = $request->deskripsi;
        if($request->filled('start_date')){
            $data->start_date = $request->start_date;
        }
        if($request->filled('end_date')){
            $data->end_date = $request->end_date;
        }

        if($request->filled('start_date_quisioner')){
            $data->start_date_quisioner = $request->start_date_quisioner;
        }
        if ($request->filled('end_date_quisioner')) {
            $data->end_date_quisioner = $request->end_date_quisioner;
        }
        if ($request->filled('minimum_target')) {
            $data->minimum_target = $request->minimum_target;
        }
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

        $_canvas_check=AssesmentCanvas::where('assesment_id',$id)->exists();
        if($_canvas_check)
        {
            return $this->errorResponse('Assesment tidak bisa dihapus',400);
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

        // if($user->status !='pending')
        // {
        //     return $this->errorResponse('User sudah melakukan verifikasi',400);
        // }

        // $_mail_check=User::where('email',$request->pic_email)->where('id','!=',$user->id)->exists();
        // if($_mail_check)
        // {
        //     return $this->errorResponse('Email .'.$request->pic_email.' sudah digunakan',400);
        // }
        // $_token = Str::random(50);
        $user->nama = $request->pic_nama;
        $user->jabatan_id = $request->pic_jabatan_id;
        $user->divisi_id = $request->pic_divisi_id;
        // $user->email = $request->pic_email;
        // $user->token = $_token;
        // $user->password = $_token;
        $user->save();

        // Notification::send($user, new InviteUserNotif($user));
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

    public function uploadReport(Request $request)
    {
        $request->validate(
            [
                'id'=>'required|exists:assesment,id',
                'docs'=>'required'
            ],
            [
                'id.required'=>'ID assement harus di isi',
                'id.exists' => 'Assement ID tidak terdaftar',
                'docs.required' => 'file laporan harus di isi',
            ]
        );
        $assesment=Assesment::find($request->id);
        if($request->hasFile('docs'))
        {
            $path = config('filesystems.path.report').'assesment/'. $assesment->id . '/report/';
            $docs = $request->file('docs');
            $filename = date('Ymdhis') . '-' . $assesment->id . '-' . $docs->hashName();
            $docs->storeAs($path, $filename);
            $filedocs = CobitHelper::Media($filename, $path, $docs);
            $assesment->docs=$filedocs;
            $assesment->save();
        }
        return $this->successResponse();
    }

    public function reportHasil(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'domain.urutan');
        $sortType = $request->get('sortType', 'asc');
        $domain_id = $request->domain_id;
        $target_id = $request->target_id;
        $search = $request->search;

        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->join('capability_target', 'assesment_canvas.assesment_id', '=', 'capability_target.assesment_id')
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at')
            ->whereNull('capability_target.deleted_at')
            ->whereNull('domain.deleted_at')
                ->select(
                    'assesment_canvas.*',
                    'domain.kode',
                    'domain.ket',
                    'domain.translate',
                    'capability_target.nama as nama_target',
                    'capability_target.id as capability_target_id',
                    )
            ->orderBy($sortBy, $sortType);


        if($request->filled('domain_id'))
        {
            $list_domain->where('assesment_canvas.domain_id',$domain_id);
        }
        if($request->filled('target_id'))
        {
            $list_domain->where('capability_target.id',$target_id);
        }
        else
        {
            $list_domain->where('capability_target.default', true);
        }
        $total = $list_domain->count();

        $offset = ($page * $limit) - $limit;
        $list_domain->limit($limit);
        $list_domain->skip($offset);
        // $list->skip($offset);

        $meta['per_page'] = (int) $limit;
        $meta['total_page'] = ceil($total / $limit);
        $meta['current_page'] = (int) $page;

        $list_domains=$list_domain->get();
        $result=[];
        if(!$list_domains->isEmpty())
        {
            foreach ($list_domains as $_item_domain) {
                $_result=$_item_domain;

                $target_org=CapabilityTargetLevel::with('target')
                    ->where('domain_id',$_item_domain->domain_id)
                    ->where('capability_target_id',$_item_domain->capability_target_id)
                    ->first();

                $_result->target_organisasi=$target_org;
                $_result->target_level = $target_org && $target_org->target != null? (int)$target_org->target:0;

                $_level = DB::table('capability_assesment')
                    ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                    ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                    ->where('capability_level.domain_id', $_item_domain->domain_id)
                    ->whereNull('capability_assesment.deleted_at')
                    ->whereNull('capability_level.deleted_at')
                    ->whereNull('capability_answer.deleted_at')
                    ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                    ->first();

                $_bobot = DB::table('capability_level')
                    ->where('domain_id', $_item_domain->domain_id)
                    ->select(DB::raw("SUM(bobot) as bobot_level"))
                    ->first();

                $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : null;
                $_bobot_level = $_bobot->bobot_level ? $_bobot->bobot_level : 0;

                $_total_compilance = 0;
                if ($_total_sum_compilance != null) {
                    $_total_compilance = round($_total_sum_compilance / $_bobot_level, 2);
                }

                $target_name='-';
                if($target_org->target != null)
                {
                    $target=CapabilityTarget::find($target_id);
                    if($target){
                        $target_name=$target->nama;
                    }
                }

                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_deskripsi='Terdapat kesenjangan antara nilai saat ini dengan target '.$target_name;
                $_result->potensi = 'Improvement pada area '.$_item_domain->translate.' dengan melakukan beberapa aktivitas tertentu sesuai rekomendasi.';

                $_result->gap_minus=(float) $_result->target_level - $_total_compilance;
                if($_total_compilance > $_result->target_level)
                {
                    $_result->gap_minus=null;
                    $_result->gap_deskripsi = 'Sudah memenuhi target ' . $target_name;
                    $_result->potensi = 'Sudah memenuhi kebutuhan '. $_item_domain->translate.', tidak ada potensi inisiatif yang perlu dilakukan pada area ini.';
                }

                // $_result->gap_minus = (float) $_item_domain->aggreed_capability_level - $_total_compilance;
                // if ($_total_compilance > $_item_domain->aggreed_capability_level) {
                //     $_result->gap_minus = null;
                //     $_result->gap_deskripsi = 'Sudah memenuhi target Manajemen KCI';
                //     $_result->potensi = 'Sudah memenuhi kebutuhan , tidak ada potensi inisiatif yang perlu dilakukan pada area ini';
                // }

                $result[]=$_result;
            }
        }

        $data['list']=$result;
        $meta['total'] = $total;
        $data['meta'] = $meta;
        return $this->successResponse($data);
    }

    public function ReportDetailOfi(Request $request)
    {
        // $data=Domain::find($request->domain_id);
        // // $data=CapabilityTarget::with(['targetlevel.domain','targetlevel.capabilityassesments'])
        // //     ->whereRelation('targetlevel','domain_id','=',$request->domain_id)
        // //     ->find($id);

        // $cap_ass=CapabilityAssesment::where('domain_id',$request->domain_id)
        //     ->where('assesment_id',$request->assesment_id)
        //     // ->where('target_id', $request->target_id)
        //     ->get();

        // $targets=CapabilityTarget::where('assesment_id',$request->assesment_id)->get();

        // $list_target=[];
        // if(!$targets->isEmpty())
        // {
        //     foreach ($targets as $_item_target) {
        //         $_target=$_item_target;
        //         $ofis=CapabilityAssesment::where('assesment_id',$request->assesment_id)
        //             ->where('domain_id',$request->domain_id)
        //             ->where('capability_target_id',$_item_target->id)
        //             ->get();

        //         $_target->listofi=$ofis;
        //         $list_target=$_target;
        //     }
        // }

        // $data->capabilityassesments=$cap_ass;
        // $data->targets = $list_target;
        $list_ofi=CapabilityAssesmentOfi::where('capability_target_id',$request->capability_target_id);

        if($request->filled('capability_assesment_id')){
            $list_ofi->where('capability_assesment_id',$request->capability_assesment_id);
        }
        $list_ofi=$list_ofi->get();
        $domain = Domain::find($request->domain_id);
        $data['ofi'] = $list_ofi;
        $data['domain']=$domain;

        if($request->filled('download')){
            return Excel::download(new AssesmentOfiByDomainExport($data), 'report-OFI-detail.xlsx');
        }
        return $this->successResponse($data);
    }

    public function downloadReportCapabilityAssesment(Request $request)
    {
        $limit = $request->limit;
        $page = $request->page;
        $domain_id = $request->domain_id;
        $target_id = $request->target_id;

        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        $list_domains = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->join('capability_target', 'assesment_canvas.assesment_id', '=', 'capability_target.assesment_id')
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at')
            ->whereNull('capability_target.deleted_at')
            ->whereNull('domain.deleted_at')
            ->select(
                'assesment_canvas.*',
                'domain.kode',
                'domain.ket',
                'domain.translate',
                'capability_target.nama as nama_target',
                'capability_target.id as capability_target_id',
            )
            ->orderBy('domain.urutan', 'asc');


        if ($request->filled('domain_id')) {
            $list_domains->where('assesment_canvas.domain_id', $domain_id);
        }
        if ($request->filled('target_id')) {
            $list_domains->where('capability_target.id', $target_id);
        } else {
            $list_domains->where('capability_target.default', true);
        }

        if($request->filled('limit') && $request->filled('page'))
        {
            $offset = ($page * $limit) - $limit;
            $list_domains->limit($limit);
            $list_domains->skip($offset);
        }

        $data = [];
        $data_list_domain=$list_domains->get();
        if (!$data_list_domain->isEmpty()) {
            foreach ($data_list_domain as $_item_domain) {
                $_result = $_item_domain;

                $target_org = CapabilityTargetLevel::with('target')
                    ->where('domain_id', $_item_domain->domain_id)
                    ->where('capability_target_id', $_item_domain->capability_target_id)
                    ->first();

                $_result->target_organisasi = $target_org;
                $_result->target_level = $target_org && $target_org->target != null ? (int) $target_org->target : 0;

                $_level = DB::table('capability_assesment')
                    ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                    ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                    ->where('capability_level.domain_id', $_item_domain->domain_id)
                    ->whereNull('capability_assesment.deleted_at')
                    ->whereNull('capability_level.deleted_at')
                    ->whereNull('capability_answer.deleted_at')
                    ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                    ->first();

                $_bobot = DB::table('capability_level')
                    ->where('domain_id', $_item_domain->domain_id)
                    ->select(DB::raw("SUM(bobot) as bobot_level"))
                    ->first();

                $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : null;
                $_bobot_level = $_bobot->bobot_level ? $_bobot->bobot_level : 0;

                $_total_compilance = 0;
                if ($_total_sum_compilance != null) {
                    $_total_compilance = round($_total_sum_compilance / $_bobot_level, 2);
                }

                $target_name = '-';
                if ($target_org->target != null) {
                    $target = CapabilityTarget::find($target_id);
                    $target_name = $target->nama;
                }

                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_deskripsi = 'Terdapat kesenjangan antara nilai saat ini dengan target ' . $target_name;
                $_result->potensi = 'Improvement pada area ' . $_item_domain->translate . ' dengan melakukan beberapa aktivitas tertentu sesuai rekomendasi.';

                $_result->gap_minus = (float) $_result->target_level - $_total_compilance;
                if ($_total_compilance > $_result->target_level) {
                    $_result->gap_minus = null;
                    $_result->gap_deskripsi = 'Sudah memenuhi target ' . $target_name;
                    $_result->potensi = 'Sudah memenuhi kebutuhan ' . $_item_domain->translate . ', tidak ada potensi inisiatif yang perlu dilakukan pada area ini.';
                }

                // $_result->gap_minus = (float) $_item_domain->aggreed_capability_level - $_total_compilance;
                // if ($_total_compilance > $_item_domain->aggreed_capability_level) {
                //     $_result->gap_minus = null;
                //     $_result->gap_deskripsi = 'Sudah memenuhi target Manajemen KCI';
                //     $_result->potensi = 'Sudah memenuhi kebutuhan , tidak ada potensi inisiatif yang perlu dilakukan pada area ini';
                // }

                $data[] = $_result;
            }
        }

        return Excel::download(new AnalisaGapExport($data),'report-capability-assesment.xlsx');
        // return $this->successResponse($data);
    }

    public function dfRiskSkenarioIN(Request $request)
    {
        $assesment_id = $request->assesment_id;
        $design_faktor_id = $request->design_faktor_id;

        $list_dfk=DB::table('quisioner_hasil_avg')
            ->join('design_faktor_komponen', 'quisioner_hasil_avg.design_faktor_komponen_id', 'design_faktor_komponen.id')
            ->join('design_faktor', 'design_faktor_komponen.design_faktor_id', 'design_faktor.id')
            ->where('quisioner_hasil_avg.assesment_id', $assesment_id)
            ->where('design_faktor.id', $design_faktor_id)
            ->whereNull('design_faktor_komponen.deleted_at')
            ->whereNull('design_faktor.deleted_at')
            ->orderBy('design_faktor_komponen.urutan', 'asc')
            ->select(
                'quisioner_hasil_avg.id',
                'design_faktor.kode as df_kode',
                'design_faktor_komponen.id as dfk_id',
                'design_faktor_komponen.nama as dfk_nama',
                'design_faktor_komponen.deskripsi as dfk_deskripsi',
                'design_faktor_komponen.baseline as dfk_baseline',
                'design_faktor_komponen.urutan as dfk_urutan',
            )->get();


        $dfks=[];
        $headercol_id = [];
        $headercol=[];
        if(!$list_dfk->isEmpty()){
            foreach ($list_dfk as $_item_dfk) {
                $item_dfk=$_item_dfk;
                $values=DB::table('quisioner_hasil_avg')
                    // ->join('design_faktor_komponen', 'quisioner_hasil_avg.design_faktor_komponen_id', 'design_faktor_komponen.id')
                    // ->join('design_faktor', 'design_faktor_komponen.design_faktor_id', 'design_faktor.id')
                    ->join('quisioner_pertanyaan','quisioner_hasil_avg.quisioner_pertanyaan_id','quisioner_pertanyaan.id')
                    ->where('quisioner_hasil_avg.assesment_id', $assesment_id)
                    ->where('quisioner_hasil_avg.design_faktor_komponen_id',$_item_dfk->dfk_id)
                    // ->where('design_faktor.id',$design_faktor_id)
                    // ->whereNull('design_faktor_komponen.deleted_at')
                    // ->whereNull('design_faktor.deleted_at')
                    ->whereNull('quisioner_pertanyaan.deleted_at')
                    // ->orderBy('design_faktor_komponen.urutan','asc')
                    ->select(
                        // 'quisioner_hasil_avg.*',
                        'quisioner_hasil_avg.avg_bobot',
                        // 'design_faktor.nama as df_nama',
                        // 'design_faktor.kode as df_kode',
                        // 'design_faktor_komponen.nama as dfk_nama',
                        // 'design_faktor_komponen.deskripsi as dfk_deskripsi',
                        // 'design_faktor_komponen.baseline as dfk_baseline',
                        'quisioner_pertanyaan.id as pertanyaan_id',
                        'quisioner_pertanyaan.pertanyaan',
                    )->get();


                $item_dfk->values=$values;
                $dfks[]=$item_dfk;

                if(!$values->isEmpty())
                {
                    // $_head_col=[];
                    foreach ($values as $_item_value) {
                        if(!in_array($_item_value->pertanyaan_id,$headercol_id))
                        {
                            $headercol_id[] = $_item_value->pertanyaan_id;

                            if($item_dfk->df_kode == 'DF3'){
                                $headercol[]=$_item_value->pertanyaan;
                            }else{
                                $headercol[] = 'Importance';
                            }
                        }
                    }
                }
            }
        }
        // $data = $this->paging($list,null,null, AssesmentDFInputSectionResource::class);
        $data['list']=$dfks;
        $data['headercol'] = $headercol;
        return $this->successResponse($data);
    }

    public function dfRiskSkenarioOUT(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $assesment_id = $request->assesment_id;
        $design_faktor_id = $request->design_faktor_id;
        $search = $request->search;

        $list = DB::table('assesment_hasil')
            ->join('domain', 'assesment_hasil.domain_id', 'domain.id')
            ->join('design_faktor', 'assesment_hasil.design_faktor_id', 'design_faktor.id')
            ->where('assesment_hasil.assesment_id', $assesment_id)
            ->where('assesment_hasil.design_faktor_id', $design_faktor_id)
            ->whereNull('domain.deleted_at')
            ->orderBy('domain.urutan', 'asc')
            ->select(
                'assesment_hasil.*',
                'design_faktor.kode as df_kode',
                'domain.kode as domain_kode',
                'domain.ket as domain_ket',
                'domain.urutan as domain_urutan',
            );

        if($request->filled('search')){
            $list->where('domain.kode', 'ilike', '%' . $search . '%');
        }
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }
}
