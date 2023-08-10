<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuisionerPilgan extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_pilgan';
    protected $keyType = 'string';
    protected $fillable = ['jawaban','sorting','quisioner_grup_pilgan_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
