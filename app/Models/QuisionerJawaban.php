<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuisionerJawaban extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_jawaban';
    protected $keyType = 'string';
    protected $fillable = ['id','jawaban','sorting','quisioner_grup_jawaban_id','bobot'];

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
