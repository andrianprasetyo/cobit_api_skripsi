<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssesmentCanvas extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $table = 'assesment_canvas';
    protected $keyType = 'string';
}
