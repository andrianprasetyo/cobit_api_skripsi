<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesainFaktor extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'desain_faktor';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi','desain_faktor_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
