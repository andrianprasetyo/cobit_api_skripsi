<?php

namespace App\Http\Resources\Repository;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepositoryUserResource extends JsonResource
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
            'nama'=>$this->nama,
            'username'=>$this->username,
            'email' => $this->email,
            'status'=>$this->status,
            'internal' => $this->internal,
        ];
    }
}
