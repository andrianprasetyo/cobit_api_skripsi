<?php

namespace App\Http\Controllers\Api;

use App\Exports\UserRespondenExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAssesmentResource;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\Organisasi;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserAssesment;
use App\Notifications\InviteUserNotif;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UsersAssesmentController extends Controller
{
    use JsonResponse;

    public function listResponden(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;

        $list = AssessmentUsers::select('assesment_users.*')
            ->join('assesment', 'assesment_users.assesment_id', '=', 'assesment.id')
            ->join('organisasi', 'assesment.organisasi_id', '=', 'organisasi.id')
            ->join('organisasi_divisi', 'assesment_users.divisi_id', '=', 'organisasi_divisi.id')
            ->leftJoin('organisasi_divisi_jabatan', 'assesment_users.jabatan_id', '=', 'organisasi_divisi_jabatan.id');

        if ($request->filled('assesment_id')) {
            $list->where('assesment_users.assesment_id', $request->assesment_id);
        }

        if ($request->filled('search')) {
            $list->where(function ($query) use ($search) {
                $query->where('assesment_users.nama', 'ilike', '%' . $search . '%')
                    ->orWhere('assesment_users.email', 'ilike', '%' . $search . '%');
            });
        }

        switch ($sortBy) {
            case 'divisi':
                $list->orderBy('organisasi_divisi.nama', $sortType);
                break;
            case 'jabatan':
                $list->orderBy('organisasi_divisi_jabatan.nama', $sortType);
                break;

            default:
                $list->orderBy($sortBy, $sortType);
                break;
        }

        $data = $this->paging($list, $request->filled('nopaging') ? null : $limit, $request->filled('nopaging') ? null : $page, UserAssesmentResource::class);
        return $this->successResponse($data);
    }


    public function detailResponden($id)
    {
        $data = AssessmentUsers::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
        return $this->successResponse(new UserAssesmentResource($data));
    }

    public function exportUser(Request $request)
    {
        $assesment=Assesment::find($request->id);
        if(!$assesment)
        {
            return $this->errorResponse('Assesment ID tidak terdaftar',404);
        }
        $data = AssessmentUsers::where('assesment_id', $request->id)->get();
        return Excel::download(new UserRespondenExport($data), 'responden-'.$assesment->nama.'.xlsx');
    }

    public function removeResponden($id)
    {
        $data=AssessmentUsers::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        if ($data->status != 'diundang') {
            return $this->errorResponse('Responden sudah melakukan quisoner', 400);
        }
        $data->delete();
        return $this->successResponse();
    }
}
