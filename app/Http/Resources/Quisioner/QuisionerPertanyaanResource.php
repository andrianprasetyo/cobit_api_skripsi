<?php

namespace App\Http\Resources\Quisioner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerPertanyaanResource extends JsonResource
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
            'pertanyaan'=>$this->pertanyaan,
            // 'design_faktor_id'=>$this->design_faktor_id,
            'quisioner_grup_jawaban_id'=>$this->quisioner_grup_jawaban_id,
            'quisioner_id'=>$this->quisioner_id,
            'sorting'=>$this->sorting,
        ];
    }
}
