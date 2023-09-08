<?php

namespace App\Http\Resources\Repository;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepositoryResource extends JsonResource
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
            // 'assesment_id'=>$this->assesment_id,
            // 'upload_by'=>$this->upload_by,
            'docs'=>$this->docs,
            'deskripsi' => $this->deskripsi,
            'assesment'=>new RepositoryAssesmentResource($this->assesment),
            'auhtor' => new RepositoryUserResource($this->auhtor),
            'created_at'=>$this->created_at,
        ];
    }
}
