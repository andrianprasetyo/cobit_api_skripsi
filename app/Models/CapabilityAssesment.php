<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityAssesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    public $incrementing = false;
    protected $table = 'capability_assesment';
    protected $keyType = 'string';
    // protected $fillable = ['kegiatan'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function files(){
        return $this->hasMany(CapabilityAssesmentEvident::class,'capability_assesment_id','id');
    }
}
