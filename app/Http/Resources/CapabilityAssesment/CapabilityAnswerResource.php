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
            'nama' => $this->nama,
            'bobot' => (float)$this->bobot,
            'label' => $this->label,
        ];
    }
}
