<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi','organisasi_id','status','deskripsi','tahun'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
