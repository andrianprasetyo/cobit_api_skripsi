<?php

namespace App\Http\Resources\Quisioner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuisionerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'=>$this->title,
            'aktif'=>$this->aktif,
            'created_at'=>$this->created_at,
        ];
    }
}
