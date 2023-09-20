<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorHistoryCapabilityResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this -> id,
            'nama' => $this -> nama,
            'username' => $this -> username,
            'email' => $this -> email,
        ];
    }
}
