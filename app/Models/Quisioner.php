<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quisioner extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner';
    protected $keyType = 'string';
    protected $fillable = ['pertanyaan','desain_faktor_id','sorting'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
