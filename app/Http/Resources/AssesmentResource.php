<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $tahun=Carbon::parse($this->tahun);
        return [
            'id'=>$this->id,
            'nama'=>$this->nama,
            'organisasi_id'=>$this->organisasi_id,
            'status'=>$this->status,
            'deskripsi'=>$this->deskripsi,
            'created_at'=>$this->created_at,
            // 'tahun'=> $tahun->format('Y-m'),
            'start_date' => $this->start_date,
            'end_date'=>$this->end_date,
            'users_id' => $this->users_id,
            'organisasi'=>$this->organisasi,
            'pic' => $this->pic,
            'start_date_quisioner' => $this->start_date_quisioner,
            'end_date_quisioner' => $this->end_date_quisioner,
            'minimum_target' => $this->minimum_target,
        ];
    }
}
