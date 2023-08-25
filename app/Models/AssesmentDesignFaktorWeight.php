<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssesmentDesignFaktorWeight extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $table = 'assesment_design_faktor_weight';
    protected $keyType = 'string';
}
