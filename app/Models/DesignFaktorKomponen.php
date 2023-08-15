<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DesignFaktorKomponen extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $table = 'design_faktor_komponen';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi', 'design_faktor_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function designfaktor()
    {
        return $this->belongsTo(DesignFaktor::class, 'design_faktor_id');
    }
}
