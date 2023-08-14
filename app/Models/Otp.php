<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory,SoftDeletes;
    public $incrementing = false;
    protected $table = 'otp';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'token','expire_at','aksi','verify_by','digunakan'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
