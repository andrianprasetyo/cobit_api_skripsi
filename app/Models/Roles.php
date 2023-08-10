<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'roles';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'code','deskripsi','aktif'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
