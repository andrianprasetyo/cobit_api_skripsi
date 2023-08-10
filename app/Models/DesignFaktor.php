<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignFaktor extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'design_faktor';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'nama', 'deskripsi'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
