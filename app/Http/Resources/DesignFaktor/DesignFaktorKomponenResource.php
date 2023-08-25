<?php

namespace App\Http\Resources\DesignFaktor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignFaktorKomponenResource extends JsonResource
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
            'nama'=>$this->nama,
            'design_faktor_id'=>$this->design_faktor_id,
            'deskripsi'=>$this->deskripsi,
            'baseline' => $this->baseline,
            'urutan' => $this->urutan,
            'designfaktor' => new DesignFaktorResource($this->designfaktor),
        ];
    }
}
