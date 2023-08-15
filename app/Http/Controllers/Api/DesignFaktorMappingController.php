<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignFaktor;
use App\Models\DesignFaktorMap;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DesignFaktorMappingController extends Controller
{
    use JsonResponse;
    public function createMap(Request $request)
    {
        $request->validate(
            [
                'design_faktor_id' => 'required|uuid|exists:design_faktor,id',
                'design_faktor_komponen_id'=>'required|uuid|exists:design_faktor_komponen,id',
                'domain_id' => 'required|uuid|exists:domain,id',
                'nilai'=>'required|decimal:2'
            ],
            [
                'design_faktor_id.required'=>'Harap pilih design faktor',
                'design_faktor_id.uuid' => 'Design faktor ID tidak valid',
                'design_faktor_id.exists' => 'Design faktor tidak terdaftar',

                'design_faktor_komponen_id.required' => 'Harap pilih design faktor komponen',
                'design_faktor_komponen_id.uuid' => 'Design faktor komponen ID tidak valid',
                'design_faktor_komponen_id.exists' => 'Design faktor komponen tidak terdaftar',

                'nilai.required'=>'Nilai harus di isi',
                'nilai.decimal' => 'Nilai harus dalam bentuk desimal (9.99)',
            ]
        );

        $data=new DesignFaktorMap();
        $data->design_faktor_id=$request->design_faktor_id;
        $data->design_faktor_komponen_id = $request->design_faktor_komponen_id;
        $data->domain_id = $request->domain_id;
        $data->nilai = $request->nilai;
        $data->save();

        return $this->successResponse();
    }

    public function detailByDF($id)
    {
        $data=DesignFaktor::with('mapping')->find($id);
        return $this->successResponse($data);
    }
}
