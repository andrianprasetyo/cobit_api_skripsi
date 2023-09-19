<?php

namespace App\Http\Resources\Quisioner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerRespondenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'assesment_id'=>$this->assesment_id,
            'email'=>$this->email,
            'nama'=>$this->nama,
            'divisi'=>$this->divisi,
            'jabatan'=>$this->jabatan,
            // 'code'=>$this->code,
            'status' => $this->status,
            'is_proses' => $this->is_proses,
            'created_at' => $this->created_at,
        ];
    }
}
