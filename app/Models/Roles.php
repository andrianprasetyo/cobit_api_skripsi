<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Roles extends Model
{
    use HasFactory, SoftDeletes, HasUuids, HasUuids;

    public $incrementing = false;
    protected $table = 'roles';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'code','deskripsi','aktif','status'];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->id = Str::uuid();
    //     });
    // }
}
