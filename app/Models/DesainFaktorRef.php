<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesainFaktorRef extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'desain_faktor_ref';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi','desain_faktor_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
