<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityAssesmentOfi extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_assesment_ofi';
    protected $keyType = 'string';
    protected $fillable = ['ofi', 'capability_assesment_id','capability_target_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
