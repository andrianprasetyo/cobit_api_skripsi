<?php

namespace App\Http\Resources\CapabilityAssesment;

use App\Http\Resources\Domain\DomainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityAssesmentLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $capabilityass = array(
            'id' => null,
            'capability_level_id' => $this->id,
            'capability_answer_id' => null,
            'note' => null,
            'ofi' => null,
        );

        if ($this->capabilityass != null) {
            $capabilityass = new CapabilityAssesmentResource($this->capabilityass);
        }
        return [
            'id' => $this->id,
            'kegiatan' => $this->kegiatan,
            'translate' => $this->translate,
            'bobot' => $this->bobot,
            'level' => $this->level,
            'urutan' => $this->urutan,
            'kode' => $this->kode,
            'subkode' => $this->domain->kode . '.' . $this->kode,
            'domain' => new DomainResource($this->domain),
            'capabilityass' => $capabilityass
        ];
    }
}
