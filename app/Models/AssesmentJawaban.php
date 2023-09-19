<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssesmentJawaban extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_jawaban';
    protected $keyType = 'string';
    protected $fillable = ['nama','bobot'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
