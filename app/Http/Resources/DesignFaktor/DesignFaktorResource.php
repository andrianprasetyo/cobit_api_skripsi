<?php

namespace App\Http\Resources\DesignFaktor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignFaktorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'deskripsi'=>$this->deskripsi,
            'sorting'=>$this->sorting,
            'created_at' => $this->created_at,
        ];
    }
}
