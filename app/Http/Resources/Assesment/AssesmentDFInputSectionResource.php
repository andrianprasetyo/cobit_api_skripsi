<?php

namespace App\Http\Resources\Assesment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentDFInputSectionResource extends JsonResource
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
            'dfk_id' => $this->dfk_id,
            'dfk_nama'=>$this->dfk_nama,
            'dfk_deskripsi'=>$this->dfk_deskripsi,
            'dfk_baseline'=>$this->dfk_baseline,
            'dfk_urutan' => $this->dfk_urutan,
            'values'=>[]
        ];
    }
}
