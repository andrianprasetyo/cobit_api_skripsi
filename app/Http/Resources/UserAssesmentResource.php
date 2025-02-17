<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAssesmentResource extends JsonResource
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
            'assesment'=>$this->assesment,
            'quesioner_processed' => $this->quesioner_processed,
            'quesioner_link'=> config('app.url_fe') . '/kuesioner/responden?code=' . $this->code
        ];
    }
}
