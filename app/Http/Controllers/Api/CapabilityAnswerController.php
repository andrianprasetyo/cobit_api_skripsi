<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Capability\CapabilityAnswerAddRequest;
use App\Models\CapabilityAnswer;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CapabilityAnswerController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $sortBy = $request->get('sortBy', 'bobot');
        $sortType = $request->get('sortType', 'asc');
        $search = $request->search;

        $list = CapabilityAnswer::query();
        if ($request->filled('search')) {
            $list->where('nama', 'ilike', '%' . $search . '%');
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list);
        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = CapabilityAnswer::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }

        return $this->successResponse($data);
    }

    public function add(CapabilityAnswerAddRequest $request)
    {
        $jawaban=$request->jawaban;

        foreach ($jawaban as $_item_jawaban) {
            $_data=new CapabilityAnswer();
            if($_item_jawaban['id'] != null)
            {
                $_data=CapabilityAnswer::find($_item_jawaban['id']);
            }
            $_data->label= strtoupper($_item_jawaban['label']);
            $_data->nama=$_item_jawaban['nama'];
            $_data->bobot = $_item_jawaban['bobot'];
            $_data->save();
        }

        return $this->successResponse();
    }

    public function remove($id)
    {
        $data = CapabilityAnswer::find($id);
        if (!$data) {
            return $this->errorResponse('Data tidak di temukan', 404);
        }
        $data->delete();

        return $this->successResponse();
    }
}
