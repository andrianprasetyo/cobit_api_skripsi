<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quisioner extends Model
{
    use HasFactory, HasUuids,SoftDeletes;

    public $incrementing = false;
    protected $table = 'quisioner';
    protected $keyType = 'string';
    protected $fillable = ['title','aktif'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->id = Str::uuid();
    //     });
    // }
}
