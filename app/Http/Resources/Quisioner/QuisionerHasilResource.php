<?php

namespace App\Http\Resources\Quisioner;

use App\Http\Resources\DesignFaktor\DesignFaktorKomponenResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerHasilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'=>$this->id,
            // 'quisioner_id'=>$this->quisioner_id,
            // 'quisioner_pertanyaan_id'=>$this->quisioner_pertanyaan_id,
            // 'jawaban_id'=>$this->jawaban_id,
            // 'assesment_users_id'=>$this->assesment_users_id,
            'bobot' => $this->bobot,
            // 'design_faktor_komponen_id'=>$this->design_faktor_komponen_id,
            'created_at' => $this->created_at,
            'quisioner'=> new QuisionerResource($this->quisioner),
            'responden' => new QuisionerRespondenResource($this->responden),
            'dfkomponen'=>new DesignFaktorKomponenResource($this->dfkomponen),
            'pertanyaan' => new QuisionerPertanyaanResource($this->pertanyaan),
            'jawaban' => new QuisionerJawabanResource($this->jawaban),
        ];
    }
}
