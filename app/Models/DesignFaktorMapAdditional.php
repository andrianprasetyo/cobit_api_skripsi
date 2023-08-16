<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DesignFaktorMapAdditional extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $table = 'design_faktor_map_additional';
    protected $keyType = 'string';
    protected $fillable = ['domain', 'design_faktor_id', 'urutan'];

    protected $hidden = [
//        'deleted_at',
        'updated_at'
    ];
}
