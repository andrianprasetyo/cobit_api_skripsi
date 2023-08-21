<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuisionerGrupJawaban extends Model
{
    use HasFactory,HasUuids,SoftDeletes;

    public $incrementing = false;
    protected $table = 'quisioner_grup_jawaban';
    protected $keyType = 'string';
    protected $fillable = ['id','nama','jenis','deskripsi'];

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

    public function jawabans()
    {
        return $this->hasMany(QuisionerJawaban::class, 'quisioner_grup_jawaban_id','id');
    }
}
