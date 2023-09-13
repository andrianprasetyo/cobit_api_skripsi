<?php

namespace App\Http\Resources\Assesment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentReportResource extends JsonResource
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
            'domain_id'=>$this->domain_id,
            'step2_init_value'=>$this->step2_init_value,
            'step2_value'=>$this->step2_value,
            'step3_init_value'=>$this->step3_init_value,
            'adjustment'=>$this->adjustment,
            'reason'=>$this->reason,
            'origin_capability_level' => $this->origin_capability_level,
            'suggest_capability_level' => $this->suggest_capability_level,
            'aggreed_capability_level' => $this->aggreed_capability_level,
            'created_at' => $this->created_at,
            'reason_adjustment' => $this->reason_adjustment,
            'assesment' => $this->assesment,
            'domain' => $this->domain,
        ];
    }
}
