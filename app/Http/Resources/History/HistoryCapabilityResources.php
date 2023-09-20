<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\History\AuthorHistoryCapabilityResources;

class HistoryCapabilityResources extends JsonResource
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
            'created_by' => $this -> created_by,
            'assesment_id' => $this -> assesment_id,
            'domain_id' => $this -> domain_id,
            'before' => $this -> before,
            'after' => $this -> after,
            'create_by_role' => $this -> create_by_role,
            'created_at' => $this -> created_at,
            'updated_at' => $this -> created_at,
            'author' => new AuthorHistoryCapabilityResources($this -> author),
        ];
    }
}
