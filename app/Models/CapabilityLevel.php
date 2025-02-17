<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityLevel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_level';
    protected $keyType = 'string';
    protected $fillable = ['kegiatan', 'translate', 'bobot', 'level', 'domain_id','urutan','kode'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class,'domain_id');
    }

    public function capabilityass()
    {
        return $this->hasOne(CapabilityAssesment::class, 'capability_level_id','id');
    }
}
