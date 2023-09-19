<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityTargetLevel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_target_level';
    protected $keyType = 'string';
    protected $fillable = ['domain_id','capability_target_id','target'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class,'domain_id');
    }

    public function capabilityassesments()
    {
        return $this->hasMany(CapabilityAssesment::class, 'capability_level_id', 'id');
    }

    public function target()
    {
        return $this->belongsTo(CapabilityTarget::class, 'capability_target_id');
    }
}
