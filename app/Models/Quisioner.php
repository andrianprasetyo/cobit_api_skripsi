<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quisioner extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner';
    protected $keyType = 'string';
    protected $fillable = ['pertanyaan','design_faktor_id','sorting'];

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
