<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisasi extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'organisasi';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'assesment_id', 'deskripsi'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];


}
