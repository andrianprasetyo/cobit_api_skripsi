<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityAssesmentEvident extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_assesment_evident';
    protected $keyType = 'string';
    protected $fillable = ['capability_assesment_id','tipe','media_repositories_id','url','deskripsi'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function docs()
    {
        return $this->belongsTo(MediaRepository::class,'media_repositories_id');
    }
}
