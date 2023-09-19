<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrupAnswerResource extends JsonResource
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
            'jenis'=>$this->jenis,
            'deskripsi'=>$this->deskripsi,
            'created_at' => $this->created_at,
        ];
    }
}
