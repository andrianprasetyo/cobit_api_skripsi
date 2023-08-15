<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuisionerPertanyaan extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_pertanyaan';
    protected $keyType = 'string';
    protected $fillable = ['pertanyaan', 'design_faktor_id', 'sorting','quisioner_id','quisioner_grup_jawaban_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
