<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data['id'] = $this->id;
        $data['assesment_id'] = $this->assesment_id;
        $data['email'] = $this->email;
        $data['nama'] = $this->nama;
        $data['divisi'] = $this->divisi;
        $data['jabatan']=$this->jabatan;
        $data['status'] = $this->status;
        $data['code'] = $this->code;
        $data['status'] = $this->status;
        $data['is_proses'] = $this->is_proses;
        $data['created_at'] = $this->created_at;
        $data['assesment']=$this->assesment;

        return $data;
    }
}
