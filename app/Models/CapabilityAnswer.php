<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityAnswer extends Model
{
    use HasFactory, SoftDeletes, HasUniqueIds;
    public $incrementing = false;
    protected $table = 'capability_answer';
    protected $keyType = 'string';
    // protected $fillable = ['kegiatan'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
