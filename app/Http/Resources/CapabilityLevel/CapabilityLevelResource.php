<?php

namespace App\Http\Resources\CapabilityLevel;

use App\Http\Resources\Domain\DomainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityLevelResource extends JsonResource
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
            'kegiatan'=>$this->kegiatan,
            'translate'=>$this->translate,
            'bobot'=>$this->bobot,
            'level'=>$this->level,
            // 'domain_id'=>$this->domain_id,
            'urutan' => $this->urutan,
            'kode' => $this->kode,
            'domain' => new DomainResource($this->domain),
        ];
    }
}
