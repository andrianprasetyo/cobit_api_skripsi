<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\Organisasi;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Notifications\InviteUserNotif;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $list = Assesment::query();
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        if($request->filled('status'))
        {
            $list->where('status',$request->status);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page);
        return $this->successResponse($data);
    }

    public function detail($id)
    {
        $data=Assesment::find($id);
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
        }

        $request->validate($validate, $validate_msg);

        DB::beginTransaction();
        try {
            // $request->validate(
            //     [
            //         'asessment' => 'required',
            //         'start_date' => 'required|date_format:Y-m-d',
            //         'end_date' => 'required|date_format:Y-m|after:start_date',
            //         'organisasi' => 'required|unique:organisasi,nama',
            //     ],
            //     [
            //         'asessment.required' => 'Nama assesment harus di isi',
            //         'start_date.required' => 'Waktu awal assesment harus di isi',
            //         'start_date.date_format' => 'Waktu awal tidak valid',
            //         'end_date.required' => 'Waktu selesai assesment harus di isi',
            //         'end_date.date_format' => 'Waktu selesai tidak valid',
            //         'end_date.after' => 'Waktu selesai harus setelah waktu mulai',
            //         'organisasi.required' => 'Harap pilih organisasi',
            //         'organisasi.unique' => 'Organisasi sudah digunakan',
            //     ]
            // );

            $organisasi_id = $request->organisasi_id;
            if ($request->filled('organisasi_id'))
            {
                $organisasi = new Organisasi();
                $organisasi->nama = $request->organisasi;
                $organisasi->deskripsi = $request->organisasi_deskripsi;
                $organisasi->save();

                $organisasi_id=$organisasi->id;
            }

            $assesment = new Assesment();
            $assesment->nama = $request->asessment;
            $assesment->deskripsi = $request->asessment_deskripsi;
            $assesment->organisasi_id = $organisasi_id;
            $assesment->tahun = $request->tahun;
            // $assesment->end_date = $request->end_date;
            $assesment->status = 'ongoing';
            $assesment->save();

            $verify_code=Str::random(50);
            $user_ass=new AssessmentUsers();
            $user_ass->assesment_id=$assesment->id;
            $user_ass->nama=$request->pic_nama;
            $user_ass->divisi = $request->pic_divisi;
            $user_ass->jabatan = $request->pic_jabatan;
            $user_ass->email = $request->pic_email;
            $user_ass->code=$verify_code;
            $user_ass->status='invited';
            $user_ass->save();

            $role=Roles::where('code','external')->first();
            if(!$role)
            {
                return $this->errorResponse('Role Asessment tidak tersedia',404);
            }

            $user=new User();
            $user->nama=$user_ass->nama;
            $user->divisi = $user_ass->divisi;
            $user->jabatan = $user_ass->jabatan;
            $user->status='pending';
            $user->internal=false;
            $user->organisasi_id=$organisasi_id;
            $user->password= $user_ass->code;
            $user->save();

            $role_user=new RoleUsers();
            $role_user->users_id = $user->id;
            $role_user->roles_id=$role->id;
            $role_user->default=true;
            $role_user->save();

            $user->assesment=$user_ass;
            $user->notify(new InviteUserNotif());

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }

    public function edit(Request $request,$id)
    {
        $data = Assesment::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        $data->delete();
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
}
