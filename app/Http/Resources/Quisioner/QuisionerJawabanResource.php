<?php

namespace App\Http\Resources\Quisioner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerJawabanResource extends JsonResource
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
            'jawaban'=>$this->jawaban,
            'bobot'=>$this->bobot,
            'sorting' => $this->sorting,
            // 'quisioner_grup_jawaban_id'=>$this->quisioner_grup_jawaban_id,
        ];
    }
}
