<?php

namespace App\Http\Resources\Capability\CapabilityTargetLevel;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityTargetLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $domain=Domain::find($this->domain_id);
        return [
            'id'=>$this->id,
            'domain_id'=>$this->domain_id,
            'capability_target_id'=>$this->capability_target_id,
            'target'=>$this->target,
            'domain'=>$domain,
        ];
    }
}
