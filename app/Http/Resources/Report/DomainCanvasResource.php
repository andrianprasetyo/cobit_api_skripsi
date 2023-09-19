<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainCanvasResource extends JsonResource
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
            'kode'=>$this->kode,
            'urutan'=>$this->urutan,
            'ket'=>$this->ket,
            'assesmentcanvas' =>new AssesmentCanvasResource($this->assesmentcanvas),
            'assesmenthasil'=> AssesmentHasilCanvasResource::collection($this->assesmenthasil),
        ];
    }
}
