<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\RespondenImport;
use App\Models\Assesment;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\Organisasi;
use App\Models\Quisioner;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Notifications\InviteRespondenNotif;
use App\Notifications\InviteUserNotif;
use App\Traits\JsonResponse;
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
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data=Assesment::with(['organisasi','pic'])->find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->successResponse($data);
    }

    public function add(Request $request)
    {

        $validate['asessment']='required';
        $validate['tahun'] = 'required|date_format:Y-m';
        // $validate['end_date'] = 'required|date_format:Y-m|after:start_date';
        $validate_msg['asessment.required']='Nama assesment harus di isi';
        $validate_msg['tahun.required'] = 'Tahun assesment harus di isi';
        $validate_msg['tahun.date_format'] = 'Tahun assesment tidak valid (Y-m)';

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

            $user=new User();
            $user->nama=$request->pic_nama;
            $user->divisi = $request->pic_divisi;
            $user->posisi = $request->pic_jabatan;
            $user->email = $request->pic_email;
            $user->status='pending';
            $user->internal=false;
            $user->organisasi_id=$organisasi_id;
            $user->password= Str::random(10);
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
            // $assesment->tahun = $request->tahun;
            $assesment->organisasi_id = $organisasi_id;
            $assesment->start_date = $request->tahun . '-' . date('d');
            $assesment->status = 'ongoing';
            $assesment->users_id=$user->id;
            $assesment->save();

            // $user_ass=new UsersAssesment();
            // $user_ass->users_id=$user->id;
            // $user_ass->assesment_id=$assesment->id;
            // $user_ass->save();

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
        $data = Assesment::with(['organisasi'])->find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->save();

        $this->successResponse();
    }

    public function remove($id)
    {
        $data = Assesment::find($id);
        if (!$data)
        {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
    }

    public function inviteRespondent(Request $request)
    {

        DB::beginTransaction();
        try {

            $validate['id'] = 'required|exists:assesment,id';
            $validate_msg['id.required'] = 'assesment ID harus di isi';
            $validate_msg['id.exists'] = 'assesment ID tidak terdaftar';

            $validate['email'] = 'required|array';
            $validate['email.*'] = 'required|email|unique:assesment_users,email';

            $validate_msg['email.required'] = 'Email harus di isi';
            $validate_msg['email.array'] = 'Email harus dalam bentuk array';
            $validate_msg['email.*.required'] = 'Email harus di isi';
            $validate_msg['email.*.email'] = 'Email tidak valid';
            $validate_msg['email.*.unique'] = 'Email sudah digunakan';

            // $validate['responden'] = 'required|array';
            // $validate['responden.required'] = 'Responden harus di isi';
            // $validate['responden.array'] = 'Responden harus dalam bentuk dalam array';

            // $validate['responden.*.nama'] = 'required';
            // $validate['responden.*.email'] = 'required|email|unique:assesment_users,email';

            // $validate_msg['responden.*.nama.required'] = 'Nama responden harus di isi';
            // $validate_msg['responden.*.email.required'] = 'Email responden harus di isi';
            // $validate_msg['responden.*.email.email'] = 'Email tidak valid';
            // $validate_msg['responden.*.email.unique'] = 'Email sudah digunakan';

            $request->validate($validate, $validate_msg);

            // return $this->successResponse();

            $assesment = Assesment::with('organisasi')->find($request->id);
            if (!$assesment) {
                return $this->errorResponse('Data tidak ditemukan', 404);
            }
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
                $responden->status = 'pending';
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

        try {

            Excel::import(new RespondenImport($request->id), $file);
            return $this->successResponse();
        } catch (\Exception $e) {
            // DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }
}
