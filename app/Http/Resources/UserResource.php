<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "nama"=>$this->nama,
            "username"=>$this->username,
            "email"=>$this->email,
            // "email_verified_at"=>$this->email_verified_at,
            "divisi"=>$this->divisi,
            "posisi"=>$this->posisi,
            "status" => $this->status,
            "internal" => $this->internal,
            "avatar" => $this->avatar,
            "organisasi_id" => $this->organisasi_id,
            "created_at" => $this->created_at,
            "roles" => $this->roles,
        ];
        // return parent::toArray($request);
    }
}
