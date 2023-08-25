<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentDesignFaktorWeightCanvasResource extends JsonResource
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
            'weight' => $this->weight,
            'assesment_id'=>$this->assesment_id,
            'designfaktor'=> new DesignFaktorCanvasResource($this->designfaktor)
        ];
    }
}
