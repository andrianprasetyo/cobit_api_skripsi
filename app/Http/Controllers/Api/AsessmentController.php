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
use App\Jobs\SetCanvasHasilDataJob;
use App\Jobs\SetProsesQuisionerHasilQueue;
use App\Models\Assesment;
use App\Models\AssesmentCanvas;
use App\Models\AssesmentDesignFaktorWeight;
use App\Models\AssesmentDocs;
use App\Models\AssesmentHasil;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentOfi;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\Domain;
use App\Models\Organisasi;
use App\Models\OrganisasiDivisi;
use App\Models\OrganisasiDivisiJabatan;
use App\Models\OrganisasiDivisiMapDF;
use App\Models\Quisioner;
use App\Models\QuisionerHasil;
use App\Models\QuisionerPertanyaan;
use App\Models\QusisionerHasilAvg;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserAssesment;
use App\Notifications\ChangeMailNotif;
use App\Notifications\InviteRespondenNotif;
use App\Notifications\InviteUserNotif;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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

        $list = Assesment::with(['organisasi', 'pic'])->withCount('users')->expire();

        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $list->where('status', $request->status);
        }

        if ($request->filled('organisasi_id')) {
            $list->where('organisasi_id', $request->organisasi_id);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page, AssesmentResource::class);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = Assesment::with([
            'pic.divisi',
            'pic.jabatan',
            'allpic:id,nama,username,email,divisi,posisi,status,internal,avatar,jabatan_id,divisi_id,pic_assesment_id',
            'allpic.divisi',
            'allpic.jabatan',
            'allpic.assesment:id,expire_at,users_id,assesment_id'
        ])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse(new AssesmentResource($data));
    }

    public function add(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $validate['asessment'] = 'required';
        // $validate['tahun'] = 'required|date_format:Y-m';

        $validate['start_date'] = 'required|date_format:Y-m-d|after_or_equal:' . $today;
        $validate['end_date'] = 'required|date_format:Y-m-d|after:start_date';

        $validate_msg['start_date.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date.after_or_equal'] = 'Tanggal mulai harus setelah tanggal sekarang';
        $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
        $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date.after'] = 'Tanggal selesai harus sesudah tanggal mulai';

        $validate_msg['asessment.required'] = 'Nama assesment harus di isi';
        // $validate_msg['tahun.required'] = 'Tahun assesment harus di isi';
        // $validate_msg['tahun.date_format'] = 'Tahun assesment tidak valid (Y-m)';

        // $validate['pic_nama']='required';
        // $validate['pic_email'] = 'required|email';
        // $validate_msg['pic_nama.required']='Nama PIC harus di isi';
        // $validate_msg['pic_email.required'] = 'Email PIC harus di isi';
        // $validate_msg['pic_email.email'] = 'Email PIC tidak valid';

        // $validate_msg['pic_email.unique'] = 'Email PIC sudah digunakan';
        // $validate['pic_expire_at'] = 'required|date_format:Y-m-d|after:today';
        // $validate_msg['pic_expire_at.required'] = 'Tanggal kadaluarsa PIC harus di isi';
        // $validate_msg['pic_expire_at.date_format'] = 'Tanggal kadaluarsa PIC tidak valid (Y-m-d)';
        // $validate_msg['pic_expire_at.after'] = 'Tanggal kadaluarsa harus setelah hari ini';

        $validate['start_date_quisioner'] = 'required|date_format:Y-m-d|after_or_equal:' . $today;
        $validate_msg['start_date_quisioner.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['start_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['start_date_quisioner.after_or_equal'] = 'Tanggal mulai quisioner harus setelah tanggal mulai assesment';

        $validate['end_date_quisioner'] = 'required|date_format:Y-m-d|before:end_date';
        $validate_msg['end_date_quisioner.required'] = 'Tanggal mulai harus di isi';
        $validate_msg['end_date_quisioner.date_format'] = 'Tanggal format (Y-m-d)';
        $validate_msg['end_date_quisioner.before'] = 'Tanggal selesai quisioner harus sebelum tanggal selesai assesment';

        if ($request->filled('organisasi_id')) {
            $validate['organisasi_id'] = 'uuid|exists:organisasi,id';
            $validate_msg['organisasi_id.uuid'] = 'Organisasi ID tidak valid';
            $validate_msg['organisasi_id.exists'] = 'Organisasi tidak terdaftar';

            // $validate['pic_divisi_id'] = 'uuid|exists:organisasi_divisi,id';
            // $validate_msg['pic_divisi_id.uuid'] = 'Jabatan ID tidak valid';
            // $validate_msg['pic_divisi_id.exists'] = 'Jabatan tidak terdaftar';

            // $validate['pic_jabatan_id'] = 'uuid|exists:organisasi_divisi_jabatan,id';
            // $validate_msg['pic_jabatan_id.uuid'] = 'Divisi ID tidak valid';
            // $validate_msg['pic_jabatan_id.exists'] = 'Divisi tidak terdaftar';

        } else {
            $validate['organisasi_nama'] = 'required|unique:organisasi,nama';
            $validate_msg['organisasi_nama.required'] = 'Nama organisasi harus di isi';
            $validate_msg['organisasi_nama.unique'] = 'Nama organisasi sudah digunakan';

            // $validate['pic_jabatan'] = 'required|string';
            // $validate['pic_divisi'] = 'required|string';
        }

        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {

            $pic_email = $request->pic_email;
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
            if (!$this->account->internal) {
                $organisasi_id = $this->account->organisasi->id;
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

            $role = Roles::where('code', 'eksternal')->first();
            if (!$role) {
                return $this->errorResponse('Role Eksternal tidak tersedia', 404);
            }

            $_check_mail_exists = User::where('email', $pic_email)->first();
            $user_id = null;
            // if (!$this->account->internal) {
            //     $user_id = $this->account->id;
            // }
            $default_ass = false;
            if (!$_check_mail_exists) {
                $_token = Str::random(50);
                $user = new User();
                $user->nama = $request->pic_nama;
                $user->divisi = $request->pic_divisi;
                $user->posisi = $request->pic_jabatan;
                $user->email = $request->pic_email;
                $user->status = 'pending';
                $user->internal = false;
                $user->organisasi_id = $organisasi_id;
                $user->jabatan_id = $pic_jabatan_id;
                $user->divisi_id = $pic_divisi_id;
                $user->token = $_token;
                $user->password = $_token;
                $user->username = Str::slug($request->pic_nama, '.');
                $user->save();

                $role_user = new RoleUsers();
                $role_user->users_id = $user->id;
                $role_user->roles_id = $role->id;
                $role_user->default = true;
                $role_user->save();

                // if($this->account->internal){
                //     $user_id=$user->id;
                // }
                $user_id = $user->id;
                $default_ass = true;
            } else {
                $user = $_check_mail_exists;
                $user_id = $_check_mail_exists->id;
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
            $assesment->end_date_quisioner = $request->end_date_quisioner;
            $assesment->status = 'ongoing';
            $assesment->users_id = $user_id;
            $assesment->minimum_target = $request->filled('minimum_target') ? $request->minimum_target : 3;
            $assesment->save();

            User::where('id', $user->id)->update([
                'pic_assesment_id' => $assesment->id
            ]);

            $user_ass = new UserAssesment();
            $user_ass->users_id = $user_id;
            $user_ass->assesment_id = $assesment->id;
            $user_ass->default = $default_ass;
            $user_ass->expire_at = $request->pic_expire_at;
            $user_ass->save();

            $target_default = new CapabilityTarget();
            $target_default->assesment_id = $assesment->id;
            $target_default->nama = 'Organisasi';
            $target_default->default = true;
            $target_default->save();

            $list_domain = Domain::all();
            if (!$list_domain->isEmpty()) {
                $payload_domain = [];
                foreach ($list_domain as $item_domain) {
                    $payload_domain[] = array(
                        'assesment_id' => $assesment->id,
                        'domain_id' => $item_domain->id,
                        'step2_init_value' => 0,
                        'step2_value' => 0,
                        'step3_init_value' => 0,
                        'step3_value' => 0,
                        'adjustment' => 0,
                        'reason' => 0,
                        'origin_capability_level' => 0,
                        'suggest_capability_level' => 0,
                        'aggreed_capability_level' => 0,
                    );
                }
                AssesmentCanvas::insert($payload_domain);
            }

            $df = DesignFaktor::get();
            foreach ($df as $item_df) {
                $checkExist = AssesmentDesignFaktorWeight::where('assesment_id', $assesment->id)->where('design_faktor_id', $item_df->id)->first();
                if (!$checkExist) {
                    $add = new AssesmentDesignFaktorWeight();
                    $add->assesment_id = $assesment->id;
                    $add->design_faktor_id = $item_df->id;
                    $add->weight = $item_df->weight;
                    $add->save();
                }
            }

            CobitHelper::generateTargetLevelDomain($assesment->id, default: true);

            // $user->assesment=$user_ass;
            // $user->notify(new InviteUserNotif());
            if (!$_check_mail_exists) {
                Notification::send($user, new InviteUserNotif($user));
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $today = Carbon::today()->format('Y-m-d');
        if ($request->filled('start_date')) {
            $validate['start_date'] = 'required|date_format:Y-m-d';
            $validate_msg['start_date.required'] = 'Tanggal mulai harus di isi';
            $validate_msg['start_date.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['start_date.after_or_equal'] = 'Tanggal mulai harus setelah tanggal sekarang';
        }

        if ($request->filled('end_date')) {
            $validate['end_date'] = 'required|date_format:Y-m-d';

            $validate_msg['end_date.required'] = 'Tanggal selesai harus di isi';
            $validate_msg['end_date.date_format'] = 'Tanggal format (Y-m-d)';
            // $validate_msg['end_date.after'] = 'Tanggal selesai assesment harus setelah tanggal mulai assesment';
        }

        if ($request->filled('start_date_quisioner')) {
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

        $data = Assesment::with(['organisasi', 'pic'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->nama = $request->nama;
        $data->deskripsi = $request->deskripsi;
        if ($request->filled('start_date')) {
            $data->start_date = $request->start_date;
        }
        if ($request->filled('end_date')) {
            $data->end_date = $request->end_date;
        }

        if ($request->filled('start_date_quisioner')) {
            $data->start_date_quisioner = $request->start_date_quisioner;
        }
        if ($request->filled('end_date_quisioner')) {
            $data->end_date_quisioner = $request->end_date_quisioner;
        }
        if ($request->filled('minimum_target')) {
            $data->minimum_target = $request->minimum_target;
        }
        if ($request->filled('user_id')) {
            $data->user_id = $request->user_id;
        }
        $data->save();

        return $this->successResponse();
    }
    public function setStatus(Request $request, $id)
    {
        $request->validate(
            [
                'status' => 'required|in:ongoing,completed',
            ],
            [
                'status.required' => 'Status harus di isi',
                'status.in' => 'Status tidak valid (ongoing|completed)',
            ]
        );

        $data = Assesment::find($id);
        $data->status = $request->status;
        $data->save();

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = Assesment::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        // $_canvas_check = AssesmentCanvas::where('assesment_id', $id)->exists();
        // if ($_canvas_check) {
        //     return $this->errorResponse('Assesment tidak bisa dihapus', 400);
        // }
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
                function ($attribute, $value, $fail) use ($request) {
                    $_chekc_exists_mail = AssessmentUsers::select('email')
                        ->where('assesment_id', $request->id)
                        ->whereIn('email', $value)
                        ->get();

                    if (!$_chekc_exists_mail->isEmpty()) {
                        $mail = [];
                        foreach ($_chekc_exists_mail as $_item_mail) {
                            $mail[] = $_item_mail->email;
                        }
                        $fail('Terdapat email yang sudah terdaftar pada assesment yang sama (' . implode(',', $mail) . ')');
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

            if ($assesment->status == 'completed') {
                return $this->errorResponse('Assesment sudah dilaksanakan/Completed', 400);
            }

            // $_exp_date=Carbon::parse($assesment->tahun);
            // if(Carbon::now()->gte($_exp_date))
            // {
            //     return $this->errorResponse('Assesment sudah lewat batas tahun ('.$_exp_date.')');
            // }
            $organisasi = $assesment->organisasi;
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


            foreach ($request->email as $_item_email) {
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
                'id' => 'required|uuid|exists:assesment,id',
                'file' => 'required|mimes:' . config('filesystems.validation.docs.excel.mimes') . '|max:' . config('filesystems.validation.docs.excel.size')
            ],
            [
                'id.required' => 'Assesment ID harus di isi',
                'id.uuid' => 'Assesment ID tidak valid',
                'id.exists' => 'Assesment ID tidak terdaftar',
                'file.required' => 'File harus di isi',
                'file.mimes' => 'File tidak valid (' . config('filesystems.validation.docs.excel.mimes') . ')',
                'file.max' => 'Maksimal file (' . config('filesystems.validation.docs.excel.size') . ')',
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
        $responden = AssessmentUsers::with(['assesment.organisasi'])->find($id);
        if (!$responden) {
            return $this->errorResponse('Responden tidak ditemukan', 404);
        }
        if ($responden->is_proses == 'done') {
            return $this->errorResponse('Responden sudah melakukan Kuisioner', 400);
        }

        $organisasi = $responden->assesment->organisasi;
        Notification::send($responden, new InviteRespondenNotif($organisasi,'Kirim ulang Undangan Kuisioner Responden'));

        return $this->successResponse();
    }

    public function addPIC(AddPICRequest $request)
    {

        $request->validated();

        $users = $request->users;
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
                    $user = User::where('email', $email)->first();
                    if (!$user) {

                        $_token = Str::random(50);
                        $user = new User();
                        $user->email = $email;
                        $user->status = 'pending';
                        $user->internal = false;
                        $user->organisasi_id = $organisasi_id;
                        $user->token = $_token;
                        $user->password = $_token;
                        $user->username = $email;
                        $user->nama = $email;
                        $user->pic_assesment_id = $assesment_id;
                        $user->save();

                        // $user_id = $user->id;

                        $role_user = new RoleUsers();
                        $role_user->users_id = $user->id;
                        $role_user->roles_id = $role->id;
                        $role_user->default = true;
                        $role_user->save();
                    }
                    $user_id = $user->id;

                    Notification::send($user, new InviteUserNotif($user));
                }

                $pic_exists = UserAssesment::where('users_id', $user_id)->where('assesment_id', $assesment_id)->first();
                if (!$pic_exists) {
                    $user_pic_exists = UserAssesment::where('assesment_id', $assesment_id)->first();
                    $user_ass = new UserAssesment();
                    $user_ass->users_id = $user_id;
                    $user_ass->assesment_id = $assesment_id;
                    $user_ass->default = false;
                    $user_ass->expire_at = $user_pic_exists->expire_at;
                    $user_ass->save();
                }

                // if (isset($_item_user['id'])) {
                //     // notif invite assesment users exists
                // }
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function editPIC(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
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
        if ($request->filled('pic_email')) {
            $_mail_check = User::where('email', $request->pic_email)->where('id', '!=', $user->id)->exists();
            if ($_mail_check) {
                return $this->errorResponse('Email .' . $request->pic_email . ' sudah digunakan', 400);
            }
            // Notification::send($user, new ChangeMailNotif($user));
            if ($user->email != $request->pic_email) {
                $user->email = $request->pic_email;
                Notification::send($user, new ChangeMailNotif($user));
            }
        }
        // $user->token = $_token;
        // $user->password = $_token;
        $user->save();

        Notification::send($user, new InviteUserNotif($user));
        return $this->successResponse();
    }

    public function reAktifasi(Request $request, $id)
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
                'id' => 'required|exists:assesment,id',
                // 'docs' => 'required',
                'filename' => 'required',
                'version' => ['required', 'regex:/^\d+\.\d+(\.\d+)?$/'],
            ],
            [
                'id.required' => 'ID assement harus di isi',
                'id.exists' => 'Assement ID tidak terdaftar',
                // 'docs.required' => 'file laporan harus di isi',
                'filename.required' => 'nama file laporan harus di isi',
                'version.required' => 'version file laporan harus di isi',
                'version.regex' => ' Version format tidak valid. gunakan format : 1.0, 1.1, 1.1.0, ...',
            ]
        );

        Db::beginTransaction();
        try {
            $assesment = Assesment::find($request->id);
            if (!$assesment) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            if ($request->hasFile('docs')) {
                if ($request->filled('parent_id')) {

                    $parent = AssesmentDocs::find($request->parent_id);

                    if ($parent) {

                        if ($parent->current) {
                            $parent->current = false;
                            $parent->save();
                        }

                        $latest_version = AssesmentDocs::where('parent_id', $request->parent_id)->latest()->first();
                        $current_version = null;
                        if ($latest_version) {
                            $current_version = $latest_version->version;
                        } else {
                            $current_version = $parent->version;
                        }

                        $version = version_compare($request->version, $current_version, '>');
                        if (!$version) {
                            return $this->errorResponse('Version harus lebih besar dari ' . $current_version, 400);
                        }
                    }

                    AssesmentDocs::where('assesment_id', $assesment->id)->where('parent_id', $request->parent_id)->where('current', true)->update([
                        'current' => false
                    ]);
                }
                $path = config('filesystems.path.report') . 'assesment/' . str_replace('-', '', $assesment->id) . '/report/';
                $docs = $request->file('docs');
                $filename = date('Ymdhis') . '-' . str_replace('-', '', $assesment->id) . '-' . $docs->hashName();
                $docs->storeAs($path, $filename);
                $filedocs = CobitHelper::Media($filename, $path, $docs);

                $ass_docs = new AssesmentDocs();
                $ass_docs->assesment_id = $assesment->id;
                $ass_docs->name = $request->filename;
                $ass_docs->version = $request->version;
                $ass_docs->parent_id = $request->parent_id;
                $ass_docs->file = $filedocs;
                $ass_docs->current = true;
                $ass_docs->save();
            }

            if ($request->filled('docs_id')) {
                AssesmentDocs::find($request->docs_id)->update([
                    'name' => $request->filename,
                    'version' => $request->version,
                ]);
            }

            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function reportHasilBACKUP(Request $request)
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


        if ($request->filled('domain_id')) {
            $list_domain->where('assesment_canvas.domain_id', $domain_id);
        }
        if ($request->filled('target_id')) {
            $list_domain->where('capability_target.id', $target_id);
        } else {
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

        $list_domains = $list_domain->get();
        $result = [];
        if (!$list_domains->isEmpty()) {
            foreach ($list_domains as $_item_domain) {
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
                    if ($target) {
                        $target_name = $target->nama;
                    }
                }

                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_deskripsi = 'Terdapat kesenjangan antara nilai saat ini dengan target ' . $target_name;
                $_result->potensi = 'Improvement pada area ' . $_item_domain->translate . ' dengan melakukan beberapa aktivitas tertentu sesuai rekomendasi.';

                $_result->gap_minus = round((float) $_result->target_level - $_total_compilance, 2);
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

                $result[] = $_result;
            }
        }

        $data['list'] = $result;
        $meta['total'] = $total;
        $data['meta'] = $meta;
        return $this->successResponse($data);
    }

    public function reportHasil(Request $request)
    {
        $target_id = $request->target_id;
        $id = $request->assesment_id;
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'domain.urutan');
        $sortType = $request->get('sortType', 'asc');

        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            // ->join('capability_target_level', 'capability_target_level.domain_id', '=', 'domain.id')
            // ->join('capability_target', 'capability_target_level.capability_target_id', '=', 'capability_target.id')
            ->where('assesment_canvas.assesment_id', $id)
            // ->where('capability_target_level.capability_target_id', $target_id)
            // ->where('capability_target.default', true)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at')
            ->select(
                'assesment_canvas.*',
                'domain.*'
            )
            ->orderBy($sortBy, $sortType);


        $total = $list_domain->count();

        $offset = ($page * $limit) - $limit;
        $list_domain->limit($limit);
        $list_domain->skip($offset);
        // $list->skip($offset);

        $meta['per_page'] = (int) $limit;
        $meta['total_page'] = ceil($total / $limit);
        $meta['current_page'] = (int) $page;

        $list_domains = $list_domain->get();
        $result = [];
        if (!$list_domains->isEmpty()) {
            foreach ($list_domains as $_item_domain) {
                $_result = $_item_domain;
                $target_org = CapabilityTargetLevel::with('target')
                    ->where('domain_id', $_item_domain->domain_id)
                    ->where('capability_target_id', $target_id)
                    ->first();

                $_result->target_organisasi = $target_org;
                $_result->target_level = $target_org && $target_org->target != null ? (int) $target_org->target : null;

                // $_level = DB::table('capability_assesment')
                //     ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                //     ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                //     ->where('capability_level.domain_id', $_item_domain->domain_id)
                //     ->whereNull('capability_assesment.deleted_at')
                //     ->whereNull('capability_level.deleted_at')
                //     ->whereNull('capability_answer.deleted_at')
                //     ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                //     ->first();

                // $_bobot = DB::table('capability_level')
                //     ->where('domain_id', $_item_domain->domain_id)
                //     ->select(DB::raw("SUM(bobot) as bobot_level"))
                //     ->first();

                // $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : null;
                // $_bobot_level = $_bobot->bobot_level ? $_bobot->bobot_level : 0;

                // $_total_compilance = 0;
                // if ($_total_sum_compilance != null && $_result->target_level) {
                //     $_total_compilance = round($_total_sum_compilance / $_bobot_level, 2);
                // }

                // $_total_compilance = DB::select
                $get_compliance_value = DB::selectOne('select get_compliance_value  from get_compliance_value(?,?)', [$id, $_item_domain->domain_id]);
                $_total_compilance = round(floatval($get_compliance_value->get_compliance_value), 2);
                $target_name = '-';
                if ($target_org && $target_org->target != null) {
                    $target = CapabilityTarget::find($target_id);
                    if ($target) {
                        $target_name = $target->nama;
                    }
                }


                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_deskripsi = 'Terdapat kesenjangan antara nilai saat ini dengan target ' . $target_name;
                $_result->potensi = 'Improvement pada area ' . $_item_domain->translate . ' dengan melakukan beberapa aktivitas tertentu sesuai rekomendasi.';

                $_result->gap_minus = round((float) $_result->target_level - $_total_compilance, 2);
                if ($_total_compilance > $_result->target_level) {
                    $_result->gap_minus = null;
                    $_result->gap_deskripsi = 'Sudah memenuhi target ' . $target_name;
                    $_result->potensi = 'Sudah memenuhi kebutuhan ' . $_item_domain->translate . ', tidak ada potensi inisiatif yang perlu dilakukan pada area ini.';
                }
                $result[] = $_result;
            }
        }

        $data['list'] = $result;
        $meta['total'] = $total;
        $data['meta'] = $meta;
        return $this->successResponse($data);
    }

    public function chartReportHasil(Request $request)
    {
        $domain_id = $request->domain_id;
        $target_id = $request->target_id;
        $assesment_id = $request->assesment_id;
        $categories = [];
        $series = [];

        $assesment = Assesment::find($assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }
        $target_default = CapabilityTarget::where('assesment_id', $assesment_id)->where('default', true)->first();
        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->join('capability_target', 'assesment_canvas.assesment_id', '=', 'capability_target.assesment_id')
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->where('capability_target.id', $target_default->id)
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

        // if ($request->filled('domain_id')) {
        //     $list_domain->where('assesment_canvas.domain_id', $domain_id);
        // }

        if ($request->filled('target_id')) {
            $list_domain->where('capability_target.id', $target_id);
        }

        // if ($request->filled('target_id')) {
        //     $list_domain->where('capability_target.id', $target_id);
        // } else {
        //     $list_domain->where('capability_target.default', true);
        // }

        $target = CapabilityTarget::find($target_id);
        $target_name = '';
        if ($target) {
            $target_name = $target->nama;
        }
        // $list_domain->where('capability_target.id', $target_id);

        $list_domain = $list_domain->get();
        $list_domains = [];

        $hasil_assesment = [];
        $gap_minus = [];
        $target_level = [];

        if (!$list_domain->isEmpty()) {
            foreach ($list_domain as $_item_domain) {
                $_result = $_item_domain;
                $list_domains[] = $_item_domain->kode;

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

                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_minus = round((float) $_result->target_level - $_total_compilance, 2);

                $gap_minus[] = $_result->gap_minus;
                $hasil_assesment[] = $_total_compilance;
                $target_level[] = $_result->target_level;
            }
        }

        $categories = $list_domains;
        $series = [
            array(
                'name' => 'Hasil Assesment & Klarifikasi',
                'data' => $hasil_assesment
            ),
            array(
                'name' => 'Target Capability Adjustment (' . $target_name . ' )',
                'data' => $target_level
            ),
        ];

        $_target = CapabilityTarget::where('assesment_id', $assesment_id)
            // ->where('capability_target.default',false)
            // ->where('domain.id', $item_domain_id)
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            // ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
            // ->select('capability_target.nama as nama_target', 'capability_target_level.target')
            ->orderBy('default', 'desc')
            ->get();

        if (!$_target->isEmpty()) {
            foreach ($_target as $item_target) {
                $_target_level = CapabilityTargetLevel::where('capability_target_id', $item_target->id)->get();
                $list_levels = [];
                // if(!$_target_level->isEmpty()){
                // }
                foreach ($_target_level as $item_level) {
                    $list_levels[] = $item_level->target ? $item_level->target : 0;
                }
                $series[] = array(
                    'name' => $item_target->nama,
                    'data' => $list_levels
                );
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        return $this->successResponse($data);
    }

    public function chartReportHasilAllTargetBACKUP(Request $request)
    {
        $domain_id = $request->domain_id;
        $target_id = $request->target_id;
        $assesment_id = $request->assesment_id;
        $categories = [];
        $series = [];

        $assesment = Assesment::find($assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }
        $target_default = CapabilityTarget::where('assesment_id', $assesment_id)->where('default', true)->first();
        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->join('capability_target', 'assesment_canvas.assesment_id', '=', 'capability_target.assesment_id')
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->where('capability_target.id', $target_default->id)
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

        // if ($request->filled('domain_id')) {
        //     $list_domain->where('assesment_canvas.domain_id', $domain_id);
        // }

        // if ($request->filled('target_id')) {
        //     $list_domain->where('capability_target.id', $target_id);
        // }

        // if ($request->filled('target_id')) {
        //     $list_domain->where('capability_target.id', $target_id);
        // } else {
        //     $list_domain->where('capability_target.default', true);
        // }

        // $target = CapabilityTarget::find($target_id);
        // $target_name = '';
        // if ($target) {
        //     $target_name = $target->nama;
        // }
        // $list_domain->where('capability_target.id', $target_id);

        $list_domain = $list_domain->get();
        $list_domains = [];

        $hasil_assesment = [];
        $gap_minus = [];
        $target_level = [];
        $list_domain_id = [];

        if (!$list_domain->isEmpty()) {
            foreach ($list_domain as $_item_domain) {
                $_result = $_item_domain;
                $list_domains[] = $_item_domain->kode;
                $list_domain_id[] = $_item_domain->domain_id;

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

                $_result->hasil_assesment = $_total_compilance;
                $_result->gap_minus = round((float) $_result->target_level - $_total_compilance, 2);

                $gap_minus[] = $_result->gap_minus;
                $hasil_assesment[] = $_total_compilance;
                $target_level[] = $_result->target_level;

            }
        }

        $categories = $list_domains;
        $series = [
            array(
                'name' => 'Hasil Assesment & Klarifikasi',
                'data' => $hasil_assesment
            ),
            array(
                'name' => 'Target Capability Adjustment',
                'data' => $target_level
            ),
        ];

        // if(!empty($list_domain_id)){
        //     foreach ($list_domain_id as $item_domain_id) {
        //         $_target = CapabilityTarget::where('capability_target.assesment_id', $assesment_id)
        //             // ->where('capability_target.default',false)
        //             ->where('domain.id', $item_domain_id)
        //             ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
        //             ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
        //             ->select('capability_target.nama as nama_target', 'capability_target_level.target')
        //             ->first();

        //         $series[]=array(
        //             'name' => $_target->nama,
        //             'data' => $_target->target
        //         );
        //     }
        // }

        $_target = CapabilityTarget::where('assesment_id', $assesment_id)
            // ->where('capability_target.default',false)
            // ->where('domain.id', $item_domain_id)
            // ->join('capability_target_level', 'capability_target.id', '=', 'capability_target_level.capability_target_id')
            // ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
            // ->select('capability_target.nama as nama_target', 'capability_target_level.target')
            ->orderBy('default', 'desc')
            ->get();

        if (!$_target->isEmpty()) {
            foreach ($_target as $item_target) {
                $_target_level = CapabilityTargetLevel::where('capability_target_id', $item_target->id)->get();
                $list_levels = [];
                // if(!$_target_level->isEmpty()){
                // }
                foreach ($_target_level as $item_level) {
                    $list_levels[] = $item_level->target ? $item_level->target : 0;
                }
                $series[] = array(
                    'name' => $item_target->nama,
                    'data' => $list_levels
                );
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        return $this->successResponse($data);
    }

    public function chartReportHasilAllTarget(Request $request)
    {
        $target = $request->target;
        $assesment_id = $request->assesment_id;
        $categories = [];
        $series = [];

        $assesment = Assesment::find($assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        if ($target == 'all') {
            $get_ist_domain = CapabilityTargetLevel::whereIn('capability_target_id', function ($q) use ($assesment_id) {
                $q->select('id')
                    ->from('capability_target')
                    ->where('assesment_id', $assesment_id)
                    ->whereNull('deleted_at');
            })
                ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
                ->join('assesment_canvas', 'assesment_canvas.domain_id', '=', 'domain.id')
                ->select('domain.id', 'domain.kode')
                ->where('assesment_canvas.assesment_id', $assesment_id)
                // ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
                ->whereNull('domain.deleted_at')
                ->orderBy('domain.urutan', 'asc')
                ->groupBy('domain.id', 'domain.kode')
                ->get();

            $list_domain_id = [];
            if (!$get_ist_domain->isEmpty()) {
                foreach ($get_ist_domain as $item_domain) {
                    $list_domain_id[] = $item_domain->id;
                    $categories[] = $item_domain->kode;
                }
            }

            $assesment_canvas = AssesmentCanvas::where('assesment_id', $assesment_id)
                ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
                ->whereIn('domain_id', $list_domain_id)
                // ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
                ->whereNull('domain.deleted_at')
                ->select(
                    DB::raw('
                assesment_canvas.domain_id,domain.kode,
                assesment_canvas.origin_capability_level,assesment_canvas.aggreed_capability_level,
                get_compliance_value(assesment_canvas.assesment_id,assesment_canvas.domain_id) as hasil_assesment'),
                )
                ->orderBy('domain.urutan', 'asc')
                ->get();


            $hasil_assesment = [];
            $hasil_adjusment = [];
            if (!$assesment_canvas->isEmpty()) {

                foreach ($assesment_canvas as $item_canvas) {
                    // $_bobot = DB::table('capability_level')
                    //     ->where('domain_id', $item_canvas->domain_id)
                    //     ->whereNull('capability_level.deleted_at')
                    //     ->select(DB::raw("SUM(bobot) as bobot_level"))
                    //     ->first();

                    // $hasil = $item_canvas->hasil_assesment != 0 || $item_canvas->hasil_assesment != null ? round(($item_canvas->hasil_assesment / $_bobot->bobot_level) + 1, 2) : 0;
                    $hasil = $item_canvas->hasil_assesment != 0 || $item_canvas->hasil_assesment != null ? round($item_canvas->hasil_assesment, 2) : 0;
                    $hasil_assesment[] = $hasil;
                    // $hasil_assesment[]= $hasil;
                    $hasil_adjusment[] = $item_canvas->aggreed_capability_level;
                    // $categories[] = $item_canvas->kode;
                }
            }

            $series = [
                array(
                    'name' => 'Hasil Assesment & Klarifikasi',
                    'data' => $hasil_assesment
                ),
                array(
                    'name' => 'Target Capability',
                    'data' => $hasil_adjusment
                ),
            ];
            $_target = CapabilityTarget::where('assesment_id', $assesment_id)
                ->orderBy('default', 'desc')
                ->get();

            if (!$_target->isEmpty()) {
                foreach ($_target as $item_target) {
                    $_target_level = CapabilityTargetLevel::where('capability_target_id', $item_target->id)
                        ->whereIn('domain_id', $list_domain_id)
                        ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
                        ->orderBy('domain.urutan', 'asc')
                        ->get();

                    $list_levels = [];
                    // if(!$_target_level->isEmpty()){
                    // }
                    foreach ($_target_level as $item_level) {
                        $list_levels[] = $item_level->target ? (int) $item_level->target : 0;
                    }
                    $series[] = array(
                        'name' => $item_target->nama == 'Organisasi' ? 'Target BUMN' : $item_target->nama,
                        'data' => $list_levels
                    );
                }
            }

            // $target_level = CapabilityTargetLevel::whereIn('capability_target_id', function ($q) use ($assesment_id) {
            //         $q->select('id')
            //             ->from('capability_target')
            //             ->where('assesment_id', $assesment_id)
            //             ->whereNull('deleted_at');
            //     })
            //     ->whereIn('domain_id',$list_domain_id)
            //     ->get();

            // $_target = CapabilityTarget::where('assesment_id', $assesment_id)
            //     ->orderBy('default', 'desc')
            //     ->get();

            // if(!$_target->isEmpty()){
            //     foreach ($_target as $item_target) {
            //         $_target_level = CapabilityTargetLevel::where('capability_target_id', $item_target->id)->whereIn('domain_id', $list_domain_id)->get();
            //         $list_levels = [];
            //         foreach ($_target_level as $item_level) {
            //             // $list_levels[] = $item_level->target ? $item_level->target : 0;
            //             $list_levels[] = $item_level->target;
            //         }
            //         $series[] = array(
            //             'name' => $item_target->nama,
            //             'data' => $list_levels
            //         );
            //     }
            // }
        } else {
            $get_ist_domain = CapabilityTargetLevel::select('domain.id', 'domain.kode')
                ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
                ->join('assesment_canvas', 'assesment_canvas.domain_id', '=', 'capability_target_level.domain_id')
                ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
                ->where('capability_target_level.capability_target_id', $target)
                ->where('assesment_canvas.assesment_id', $assesment_id)
                ->whereNull('domain.deleted_at')
                ->orderBy('domain.urutan', 'asc')
                ->groupBy('domain.id', 'domain.kode')
                ->get();

            $list_domain_id = [];
            if (!$get_ist_domain->isEmpty()) {
                foreach ($get_ist_domain as $item_domain) {
                    $list_domain_id[] = $item_domain->id;
                    $categories[] = $item_domain->kode;
                }
            }

            $assesment_canvas = AssesmentCanvas::where('assesment_id', $assesment_id)
                ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
                ->whereIn('domain_id', $list_domain_id)
                ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
                ->whereNull('domain.deleted_at')
                ->select(
                    DB::raw('
                assesment_canvas.domain_id,
                assesment_canvas.origin_capability_level,assesment_canvas.aggreed_capability_level,
                get_compliance_value(assesment_canvas.assesment_id,assesment_canvas.domain_id) as hasil_assesment'),
                )
                ->orderBy('domain.urutan', 'asc')
                ->get();


            $hasil_assesment = [];
            $hasil_adjusment = [];
            if (!$assesment_canvas->isEmpty()) {

                foreach ($assesment_canvas as $item_canvas) {
                    $hasil = $item_canvas->hasil_assesment != 0 || $item_canvas->hasil_assesment != null ? round($item_canvas->hasil_assesment, 2) : 0;
                    $hasil_assesment[] = $hasil;
                    // $hasil_assesment[]= $hasil;
                    $hasil_adjusment[] = $item_canvas->aggreed_capability_level;
                    // $categories[] = $item_domain->kode;
                }
            }

            $series = [
                array(
                    'name' => 'Hasil Assesment & Klarifikasi',
                    'data' => $hasil_assesment
                ),
                array(
                    'name' => 'Target Capability Adjustment',
                    'data' => $hasil_adjusment
                ),
            ];

            $_target = CapabilityTarget::where('assesment_id', $assesment_id)
                ->where('id', $target)
                ->orderBy('default', 'desc')
                ->get();

            if (!$_target->isEmpty()) {
                foreach ($_target as $item_target) {
                    $_target_level = CapabilityTargetLevel::where('capability_target_id', $item_target->id)
                        ->whereIn('domain_id', $list_domain_id)
                        ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
                        ->orderBy('domain.urutan', 'asc')
                        ->get();

                    $list_levels = [];
                    foreach ($_target_level as $item_level) {
                        $list_levels[] = $item_level->target ? (int) $item_level->target : 0;
                    }
                    $series[] = array(
                        'name' => $item_target->nama == 'Organisasi' ? 'Target BUMN' : $item_target->nama,
                        'data' => $list_levels
                    );
                }
            }
        }


        $data['categories'] = $categories;
        $data['series'] = $series;
        return $this->successResponse($data);
    }
    public function ReportDetailOfi(Request $request)
    {

        // $list_ofi = CapabilityAssesmentOfi::where('capability_target_id', $request->capability_target_id);
        $list_ofi = CapabilityAssesmentOfi::query();

        /*
        if($request->filled('capability_assesment_id')){
            $list_ofi->where('capability_assesment_id',$request->capability_assesment_id);
        }
        */

        if ($request->filled('domain_id')) {
            $list_ofi->where('domain_id', $request->domain_id);
        }
        $list_ofi = $list_ofi->get();
        $domain = Domain::find($request->domain_id);
        $data['ofi'] = $list_ofi;
        $data['domain'] = $domain;

        if ($request->filled('download')) {
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

        if ($request->filled('limit') && $request->filled('page')) {
            $offset = ($page * $limit) - $limit;
            $list_domains->limit($limit);
            $list_domains->skip($offset);
        }

        $data = [];
        $data_list_domain = $list_domains->get();
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

        return Excel::download(new AnalisaGapExport($data, $target_name), 'report-capability-assesment.xlsx');
        // return $this->successResponse($data);
    }

    public function dfRiskSkenarioIN(Request $request)
    {
        $assesment_id = $request->assesment_id;
        $design_faktor_id = $request->design_faktor_id;

        $list_dfk = DB::table('quisioner_hasil_avg')
            ->join('design_faktor_komponen', 'quisioner_hasil_avg.design_faktor_komponen_id', 'design_faktor_komponen.id')
            ->join('design_faktor', 'design_faktor_komponen.design_faktor_id', 'design_faktor.id')
            ->where('quisioner_hasil_avg.assesment_id', $assesment_id)
            ->where('design_faktor.id', $design_faktor_id)
            ->whereNull('design_faktor_komponen.deleted_at')
            ->whereNull('design_faktor.deleted_at')
            ->orderBy('design_faktor_komponen.urutan', 'asc')
            ->select(
                'design_faktor_komponen.id as dfk_id',
                'design_faktor.kode as df_kode',
                'design_faktor_komponen.nama as dfk_nama',
                'design_faktor_komponen.deskripsi as dfk_deskripsi',
                'design_faktor_komponen.baseline as dfk_baseline',
                'design_faktor_komponen.urutan as dfk_urutan',
            )
            ->groupBy('dfk_id', 'df_kode')
            ->get();


        $dfks = [];
        $headercol_id = [];
        $headercol = [];
        if (!$list_dfk->isEmpty()) {
            foreach ($list_dfk as $_item_dfk) {
                $item_dfk = $_item_dfk;
                $values = DB::table('quisioner_hasil_avg')
                    // ->join('design_faktor_komponen', 'quisioner_hasil_avg.design_faktor_komponen_id', 'design_faktor_komponen.id')
                    // ->join('design_faktor', 'design_faktor_komponen.design_faktor_id', 'design_faktor.id')
                    ->join('quisioner_pertanyaan', 'quisioner_hasil_avg.quisioner_pertanyaan_id', 'quisioner_pertanyaan.id')
                    ->where('quisioner_hasil_avg.assesment_id', $assesment_id)
                    ->where('quisioner_hasil_avg.design_faktor_komponen_id', $_item_dfk->dfk_id)
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
                    )
                    ->get();


                $item_dfk->values = $values;
                $dfks[] = $item_dfk;

                if (!$values->isEmpty()) {
                    // $_head_col=[];
                    foreach ($values as $_item_value) {
                        if (!in_array($_item_value->pertanyaan_id, $headercol_id)) {
                            $headercol_id[] = $_item_value->pertanyaan_id;

                            if ($item_dfk->df_kode == 'DF3') {
                                $headercol[] = $_item_value->pertanyaan;
                            } else {
                                $headercol[] = 'Importance';
                            }
                        }
                    }
                }
            }
        }
        // $data = $this->paging($list,null,null, AssesmentDFInputSectionResource::class);
        $data['list'] = $dfks;
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
                // 'assesment_hasil.*',
                // 'assesment_hasil.id',
                'assesment_hasil.assesment_id',
                'assesment_hasil.design_faktor_id',
                'assesment_hasil.domain_id',
                'assesment_hasil.relative_importance',
                'assesment_hasil.score',
                'assesment_hasil.baseline_score',
                'design_faktor.kode as df_kode',
                'domain.kode as domain_kode',
                'domain.ket as domain_ket',
                'domain.urutan as domain_urutan',
            )
            ->groupBy(
                // 'assesment_hasil.id',
                'assesment_hasil.assesment_id',
                'assesment_hasil.design_faktor_id',
                'assesment_hasil.domain_id',
                'assesment_hasil.relative_importance',
                'assesment_hasil.score',
                'assesment_hasil.baseline_score',
                'design_faktor.kode',
                'domain.kode',
                'domain.ket',
                'domain.urutan',
            );

        if ($request->filled('search')) {
            $list->where('domain.kode', 'ilike', '%' . $search . '%');
        }
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function dfRiskSkenarioOUTChart(Request $request)
    {
        $assesment_id = $request->assesment_id;
        $design_faktor_id = $request->design_faktor_id;
        $_list_domain = Domain::orderBy('urutan', 'ASC')->get();

        $series = [];
        $categories = [];

        $score = [];
        $baseline_score = [];
        $relative_importance = [];
        if (!$_list_domain->isEmpty()) {
            foreach ($_list_domain as $_item_domain) {
                $categories[] = $_item_domain->kode;

                $nilai = DB::table('assesment_hasil')
                    ->join('domain', 'assesment_hasil.domain_id', 'domain.id')
                    ->join('design_faktor', 'assesment_hasil.design_faktor_id', 'design_faktor.id')
                    ->where('domain.id', $_item_domain->id)
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
                    )->first();

                $score[] = $nilai ? CobitHelper::convertToNumber($nilai->score) : 0;
                $baseline_score[] = $nilai ? CobitHelper::convertToNumber($nilai->baseline_score) : 0;
                $relative_importance[] = $nilai ? CobitHelper::convertToNumber($nilai->relative_importance) : 0;
            }

            $series = array(
                // [
                //     'name' => 'Score',
                //     'data' => $score
                // ],
                // [
                //     'name' => 'Baseline Score',
                //     'data' => $baseline_score
                // ],
                [
                    'name' => 'Relative Importance',
                    'data' => $relative_importance
                ]
            );
        }

        $data['categories'] = $categories;
        $data['series'] = $series;

        return $this->successResponse($data);
    }

    public function editPicExpire(Request $request, $id)
    {
        $row = UserAssesment::find($id);
        if (!$row) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        $row->expire_at = $request->expire_at;
        $row->save();

        $data = Assesment::with('pic.divisi', 'pic.jabatan')->find($row->assesment_id);
        return $this->successResponse(new AssesmentResource($data));
    }

    public function listCurrentDocs(Request $request)
    {
        $list = AssesmentDocs::where('assesment_id', $request->assesment_id);
        if ($request->filled('parent_id')) {
            $list->where('id', $request->parent_id);
            $list->orWhere('parent_id', $request->parent_id);
        } else {
            $list->where('current', true);
        }

        $data = $list->orderByDesc('created_at')->get();
        return $this->successResponse($data);
    }

    public function detailDocs($id)
    {
        $data = AssesmentDocs::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function removeDocs($id)
    {
        DB::beginTransaction();
        try {
            $data = AssesmentDocs::find($id);
            if (!$data) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }

            if ($data->parent_id) {
                $list_deleted = AssesmentDocs::where('id', $data->parent_id)->orWhere('parent_id', $data->parent_id)->get();
                if (!$list_deleted->isEmpty()) {
                    foreach ($list_deleted as $item_doc) {
                        if (Storage::exists($item_doc->file->path)) {
                            Storage::delete($item_doc->file->path);
                        }
                        $item_doc->delete();
                    }
                }
                // AssesmentDocs::where('id',$data->parent_id)->orWhere('parent_id', $data->parent_id)->delete();
            }

            if (Storage::exists($data->file->path)) {
                Storage::delete($data->file->path);
            }

            $data->delete();
            DB::commit();
            return $this->successResponse(Storage::exists($data->file->path));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateDocs(Request $request, $id)
    {
        $data = AssesmentDocs::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->name = $request->filename;
        $data->save();

        return $this->successResponse();
    }

    public function changeOrg(Request $request, $id)
    {
        $validate = [];
        $validate_msg = [];
        if ($request->filled('organisasi_id')) {
            $validate['organisasi_id'] = 'uuid|exists:organisasi,id';
            $validate_msg['organisasi_id.uuid'] = 'Organisasi ID tidak valid';
            $validate_msg['organisasi_id.exists'] = 'Organisasi tidak terdaftar';

        } else {
            $validate['organisasi_nama'] = 'required|unique:organisasi,nama';
            $validate_msg['organisasi_nama.required'] = 'Nama organisasi harus di isi';
            $validate_msg['organisasi_nama.unique'] = 'Nama organisasi sudah digunakan';
        }
        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {

            $org_id = null;
            $data = null;
            $assesment = Assesment::find($id);
            if (!$assesment) {
                return $this->errorResponse('Project tidak ditemukan', 404);
            }
            if ($request->filled('organisasi_id')) {
                $org_id = $request->organisasi_id;
                $data = Organisasi::find($request->organisasi_id);
            } else {
                $organisasi = new Organisasi();
                $organisasi->nama = $request->organisasi_nama;
                $organisasi->deskripsi = $request->organisasi_deskripsi;
                $organisasi->save();

                $org_id = $organisasi->id;
                $data = $organisasi;
            }

            $assesment->organisasi_id = $org_id;
            $assesment->save();

            DB::commit();
            return $this->successResponse($data);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }

    // jangan digunakan dulu
    public function addNewPIC(Request $request)
    {
        $validation['nama'] = 'required';
        $msg_val['nama.required'] = 'Nama harus di isi';
        $validation['username'] = 'required';
        $msg_val['username.required'] = 'Username harus di isi';

        $validation['email'] = 'email|unique:users,email';
        $msg_val['email.email'] = 'Email tidak valid';
        $msg_val['email.unique'] = 'Email sudah digunakan';

        $validation['role_id'] = 'required|uuid|exists:roles,id';
        $msg_val['role_id.required'] = 'Role harus di isi';
        $msg_val['role_id.uuid'] = 'Role ID tidak valid';
        $msg_val['role_id.exists'] = 'Role tidak terdaftar';

        $validation['organisasi_id'] = 'required|uuid|exists:organisasi,id';
        $msg_val['organisasi_id.required'] = 'Organisasi harus di isi';
        $msg_val['organisasi_id.uuid'] = 'Organisasi ID tidak valid';
        $msg_val['organisasi_id.exists'] = 'Organisasi tidak terdaftar';

        $validation['assesment_id'] = 'required|uuid|exists:assesment,id';
        $msg_val['assesment_id.required'] = 'Assesment harus di isi';
        $msg_val['assesment_id.uuid'] = 'Assesment ID tidak valid';
        $msg_val['assesment_id.exists'] = 'Assesment tidak terdaftar';
        $request->validate($validation, $msg_val);

        $role = Roles::where('code', 'eksternal')->first();
        if (!$role) {
            return $this->errorResponse('Role Eksternal tidak tersedia', 404);
        }
        DB::beginTransaction();
        try {
            $user = new User();
            $user->email = $request->email;
            $user->status = 'active';
            $user->internal = false;
            $user->organisasi_id = $request->organisasi_id;
            // $user->token = $_token;
            $user->password = 'admin';
            $user->username = $request->username;
            $user->pic_assesment_id = $request->assesment_id;
            $user->save();

            $role_user = new RoleUsers();
            $role_user->users_id = $user->id;
            $role_user->roles_id = $role->id;
            $role_user->default = true;
            $role_user->save();

            $user_ass = new UserAssesment();
            $user_ass->users_id = $user->id;
            $user_ass->assesment_id = $request->assesment_id;
            $user_ass->default = false;
            $user_ass->save();
            DB::commit();
            //
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }

    public function reKalkulasi($id)
    {
        $data = Assesment::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        try {
            $list_backup_hasil_quisioner = [];
            $quisionerId = Quisioner::where('aktif', true)->first();
            $user_assessment = AssessmentUsers::where('assesment_id', $id)
                ->where('status', 'done')
                ->where('quesioner_processed', true)
                ->get();
            if (!$user_assessment->isEmpty()) {
                foreach ($user_assessment as $item_user) {

                    $dfList = DB::select("SELECT * FROM design_faktor where id not in (
                    select design_faktor_id from quisioner_hasil qh JOIN design_faktor_komponen dfk ON dfk.id=qh.design_faktor_komponen_id JOIN design_faktor df ON df.id=dfk.design_faktor_id where qh.assesment_users_id=:assesment_users_id AND qh.deleted_at is null GROUP BY design_faktor_id)", ['assesment_users_id' => $item_user->id]);
                    foreach ($dfList as $df) {
                        $dfKomponen = DesignFaktorKomponen::where('design_faktor_id', $df->id)->get();
                        $pertanyaanId = QuisionerPertanyaan::where('design_faktor_id', $df->id)->first();
                        if ($df->kode == 'DF3') {
                            for ($a = 1; $a <= 2; $a++) {
                                foreach ($dfKomponen as $dfk) {
                                    $find = QuisionerHasil::where('assesment_users_id', $item_user->id)->where('design_faktor_komponen_id', $dfk->id)->get()->count();
                                    if ($find <= 2) {
                                        $ins = new QuisionerHasil();
                                        $ins->quisioner_id = $quisionerId->id;
                                        $ins->quisioner_pertanyaan_id = $pertanyaanId->id;
                                        $ins->jawaban_id = null;
                                        $ins->assesment_users_id = $item_user->id;
                                        $ins->bobot = null;
                                        //$ins->is_proses
                                        $ins->design_faktor_komponen_id = $dfk->id;
                                        $ins->save();
                                    }

                                }
                            }
                        } else {
                            foreach ($dfKomponen as $dfk) {
                                $find = QuisionerHasil::where('assesment_users_id', $item_user->id)->where('design_faktor_komponen_id', $dfk->id)->first();
                                if (!$find) {
                                    $ins = new QuisionerHasil();
                                    $ins->quisioner_id = $quisionerId->id;
                                    $ins->quisioner_pertanyaan_id = $pertanyaanId->id;
                                    $ins->jawaban_id = null;
                                    $ins->assesment_users_id = $item_user->id;
                                    $ins->bobot = null;
                                    //$ins->is_proses
                                    $ins->design_faktor_komponen_id = $dfk->id;
                                    $ins->save();
                                }
                            }
                        }
                    }

                    $list_df_map = OrganisasiDivisiMapDF::where('organisasi_divisi_id', $item_user->divisi_id)->get();
                    if (!$list_df_map->isEmpty()) {
                        $list_not_in_df_komp = [];
                        foreach ($list_df_map as $item_map_df) {
                            $list_df_komponen = DesignFaktorKomponen::where('design_faktor_id', $item_map_df->design_faktor_id)->get();
                            if (!$list_df_komponen->isEmpty()) {

                                foreach ($list_df_komponen as $item_df_komponen) {
                                    $list_not_in_df_komp[] = $item_df_komponen->id;

                                    // $data_backup = QuisionerHasil::where('assesment_users_id', $item_user->id)
                                    //     ->where('quisioner_id', $quisionerId->id)
                                    //     ->where('design_faktor_komponen_id',$item_df_komponen->id)
                                    //     ->first();
                                    // if($data_backup){
                                    //     $list_backup_hasil_quisioner[]=$data_backup;
                                    // }

                                    // DB::table('quisioner_hasil_backup')->insertUsing(
                                    //     ['id', 'quisioner_id', 'quisioner_pertanyaan_id', 'jawaban_id','assesment_users_id','bobot','is_proses','design_faktor_komponen_id','created_at'], // Kolom di tabel tujuan
                                    //     DB::table('quisioner_hasil')
                                    //         ->select('id', 'quisioner_id', 'quisioner_pertanyaan_id', 'jawaban_id', 'assesment_users_id', 'bobot', 'is_proses', 'design_faktor_komponen_id', 'created_at')
                                    //         ->where('assesment_users_id', $item_user->id)
                                    //         ->where('quisioner_id', $quisionerId->id)
                                    //         ->where('design_faktor_komponen_id', $item_df_komponen->id)
                                    //         ->whereNull('deleted_at')
                                    // );

                                }
                            }
                        }

                        QuisionerHasil::where('assesment_users_id', $item_user->id)
                            ->where('quisioner_id', $quisionerId->id)
                            ->whereNotIn('design_faktor_komponen_id', $list_not_in_df_komp)
                            ->update([
                                'jawaban_id' => null,
                                'bobot' => null
                            ]);


                        // QuisionerHasil::where('assesment_users_id', $item_user->id)
                        //     ->where('quisioner_id', $quisionerId->id)
                        //     ->whereNotIn('design_faktor_komponen_id', $list_not_in_df_komp)
                        //     ->delete();
                    }

                    SetProsesQuisionerHasilQueue::dispatch($item_user->id);
                }
            }

            if (!empty($list_backup_hasil_quisioner)) {
                $QuisionerHasilBackupExists = DB::select("SELECT to_regclass('public.quisioner_hasil_backup')");
                if (!empty($QuisionerHasilBackupExists) && $QuisionerHasilBackupExists[0]->to_regclass !== null) {
                    DB::table('quisioner_hasil_backup')->insert($list_backup_hasil_quisioner);
                }
            }

            SetCanvasHasilDataJob::dispatch($id);
            DB::commit();
            return $this->successResponse();
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->errorResponse($th->getMessage());
        }
    }

    public function removePIC(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        $user->pic_assesment_id = null;
        $user->save();
        return $this->successResponse();
    }
    public function runCobitHelperManual(Request $request)
    {
        $id = $request->assesment_id;
        $step = $request->step;
        try {
            switch ($step) {
                case 1:
                    CobitHelper::assesmentDfWeight($id);
                    CobitHelper::setCanvasStep2Value($id);
                    CobitHelper::setCanvasStep3Value($id);
                    CobitHelper::updateCanvasAdjust($id);
                    CobitHelper::generateTargetLevelDomain($id, 'Organisasi', true);
                    break;

                case 2:
                    CobitHelper::setCanvasStep2Value($id);
                    CobitHelper::setCanvasStep3Value($id);
                    CobitHelper::updateCanvasAdjust($id);
                    CobitHelper::generateTargetLevelDomain($id, 'Organisasi', true);
                    break;

                case 3:
                    CobitHelper::setCanvasStep3Value($id);
                    CobitHelper::updateCanvasAdjust($id);
                    CobitHelper::generateTargetLevelDomain($id, 'Organisasi', true);
                    break;

                case 4:
                    CobitHelper::updateCanvasAdjust($id);
                    CobitHelper::generateTargetLevelDomain($id, 'Organisasi', true);
                    break;

                case 5:
                    CobitHelper::generateTargetLevelDomain($id, 'Organisasi', true);
                    break;
                default:
                    # code...
                    break;
            }
            // if($method == 'generateTargetLevelDomain'){
            //     $data = CobitHelper::generateTargetLevelDomain($id,default:true);
            // }else{
            //     $data= CobitHelper::{$method}($id);
            // }
            // if (!empty($method)) {
            //     foreach ($method as $item) {
            //         if ($item == 'generateTargetLevelDomain') {
            //             CobitHelper::generateTargetLevelDomain($id, default: true);
            //         } else {
            //             CobitHelper::{$item}($id);
            //         }
            //     }
            // }
            // SetCanvasHasilDataJob::dispatch($id);
            return $this->successResponse();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
