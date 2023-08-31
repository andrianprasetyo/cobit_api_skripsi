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

        $list = AssessmentUsers::with(['assesment']);

        if($request->filled('assesment_id'))
        {
            $list->where('assesment_id',$request->assesment_id);
        }
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
            $list->orWhere('email', 'ilike', '%' . $search . '%');
        }
        $list->orderBy($sortBy, $sortType);

        $data = $this->paging($list, $limit, $page, UserAssesmentResource::class);
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
        $data=AssessmentUsers::find();
        if(!$data)
        {
            return $this->errorResponse('Data tidak ditemukan',404);
        }
        $data->delete();
        return $this->successResponse();
    }
}
