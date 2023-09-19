<?php

namespace App\Http\Resources\Quisioner;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerAssesmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $start_date = Carbon::parse($this->start_date);
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'organisasi_id' => $this->organisasi_id,
            'status' => $this->status,
            'deskripsi' => $this->deskripsi,
            'created_at' => $this->created_at,
            'start_date' => $start_date->format('Y-m'),
            // 'end_date'=>$this->end_date,
            'users_id' => $this->users_id,
            'organisasi' => $this->organisasi,
            'pic' => $this->pic,
        ];
    }
}
