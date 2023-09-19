<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentCanvasResource extends JsonResource
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
            'adjustment'=>$this->adjustment,
            'aggreed_capability_level' => $this->aggreed_capability_level,
            'origin_capability_level' => $this->origin_capability_level,
            'reason' => $this->reason,
            'reason_adjustment' => $this->reason_adjustment,
            'step2_init_value' => $this->step2_init_value,
            'step2_value' => $this->step2_value,
            'step3_init_value' => $this->step3_init_value,
            'step3_value' => $this->step3_value,
            'suggest_capability_level' => $this->suggest_capability_level,
            // 'assesment_id' => $this->assesment_id,
            // 'domain_id' => $this->domain_id,
        ];
    }
}
