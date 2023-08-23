<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'code'=>$this->code,
            'deskripsi'=>$this->deskripsi,
            'aktif' => $this->aktif,
            'created_at' => $this->created_at,
        ];
        // return parent::toArray($request);
    }
}
