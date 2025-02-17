<?php

namespace App\Http\Resources\Domain;

use App\Models\Assesment;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainByAssesmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ass=Assesment::find($this->assesment_id);
        $is_assessment = null;
        if($ass){
            $is_assessment = $this->aggreed_capability_level >= $ass->minimum_target?'Ya':'Tidak';
        }
        $capability_target_default = CapabilityTarget::where('assesment_id', $this->assesment_id)->where('default', true)->first();
        $target=null;
        if($capability_target_default){
            $target_level = CapabilityTargetLevel::where('capability_target_id',$capability_target_default->id)
                ->where('domain_id',$this->domain_id)
                ->first();
            if($target_level){
                $target= (int)$target_level->target;
            }
        }
        return [
            'id'=>$this->id,
            'adjustment'=>$this->adjustment,
            'aggreed_capability_level'=>$this->aggreed_capability_level,
            'assesment_id'=>$this->assesment_id,
            'domain_id'=>$this->domain_id,
            'kode'=>$this->kode,
            'ket'=>$this->ket,
            'origin_capability_level'=>$this->origin_capability_level,
            'reason'=>$this->reason,
            'reason_adjustment'=>$this->reason_adjustment,
            'step2_init_value'=>$this->step2_init_value,
            'step2_value'=>$this->step2_value,
            'step3_init_value'=>$this->step3_init_value,
            'step3_value'=>$this->step3_value,
            'suggest_capability_level' => $this->suggest_capability_level,
            'urutan' => $this->urutan,
            'target' => $target,
            'is_assessment'=> $is_assessment
        ];
    }
}
