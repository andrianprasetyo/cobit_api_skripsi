<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuisionerGrupJawaban extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_grup_jawaban';
    protected $keyType = 'string';
    protected $fillable = ['nama','jenis'];

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

    public function jawabans()
    {
        return $this->hasMany(QuisionerJawaban::class, 'quisioner_grup_jawaban_id','id');
    }
}
