<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QuisionerJawaban extends Model
{
    use HasFactory,SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'quisioner_jawaban';
    protected $keyType = 'string';
    protected $fillable = ['id','jawaban','sorting','quisioner_grup_jawaban_id','bobot'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function grup()
    {
        return $this->hasOne(QuisionerGrupJawaban::class,'id','quisioner_grup_jawaban_id');
    }
}
