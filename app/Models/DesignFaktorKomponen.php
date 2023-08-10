<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignFaktorKomponen extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'design_faktor_komponen';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi', 'design_faktor_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
