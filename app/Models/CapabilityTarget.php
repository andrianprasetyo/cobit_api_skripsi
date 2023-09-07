<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityTarget extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_target';
    protected $keyType = 'string';
    protected $fillable = ['nama','assesment_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class,'assesment_id');
    }

    public function capabilitytargetlevel()
    {
        return $this->hasMany(CapabilityTargetLevel::class,'capability_target_id','id');
    }
}
