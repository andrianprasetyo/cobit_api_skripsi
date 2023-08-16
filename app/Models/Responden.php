<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responden extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'roles';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'divisi', 'posisi', 'email', 'projek_id','status'];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];
}
