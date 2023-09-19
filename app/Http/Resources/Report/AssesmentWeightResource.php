<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentWeightResource extends JsonResource
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
            'design_faktor_id'=>$this->design_faktor_id,
            'weight'=>(float)$this->weight,
        ];
    }
}
