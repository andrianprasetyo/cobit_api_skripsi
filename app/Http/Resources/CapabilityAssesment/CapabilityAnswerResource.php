<?php

namespace App\Http\Resources\CapabilityAssesment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityAnswerResource extends JsonResource
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
            'capability_level_id' => $this->capability_level_id,
            'capability_answer_id' => $this->capability_answer_id,
            'note' => $this->note,
            'ofi' => $this->ofi,
        ];
    }
}
