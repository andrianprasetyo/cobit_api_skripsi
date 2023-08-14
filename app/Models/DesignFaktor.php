<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DesignFaktor extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'design_faktor';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'nama', 'deskripsi','pertanyaan','quisioner_grup_jawaban_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
