<?php

namespace App\Http\Resources\CapabilityLevel;

use App\Http\Resources\Domain\DomainResource;
use App\Models\Domain;
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
        $domain=Domain::find($this->domain_id);
        $subcode='.'.$this->kode;
        if($domain)
        {
            $subcode=$domain->kode.'.'.$this->kode;
        }
        return [
            'id'=>$this->id,
            'kegiatan'=>$this->kegiatan,
            'translate'=>$this->translate,
            'bobot'=>$this->bobot,
            'level'=>$this->level,
            'urutan' => $this->urutan,
            'kode' => $this->kode,
            'subkode' => $subcode,
            'domain' => $domain,
        ];
    }
}
