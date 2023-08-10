<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuisionerGrupPilgan extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_grup_pilgan';
    protected $keyType = 'string';
    protected $fillable = ['nama'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
