<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignFaktorCanvasResource extends JsonResource
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
            'kode' => $this->kode,
            'nama' => $this->nama,
            'urutan' => $this->urutan,
            'weight' => $this->weight,
            'assesmentweight'=>$this->assesmentweight,
        ];
    }
}
