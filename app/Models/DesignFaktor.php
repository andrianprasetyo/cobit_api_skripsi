<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DesignFaktor extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'design_faktor';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'nama', 'deskripsi','weight','urutan'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function mapping()
    {
        return $this->hasMany(DesignFaktorMap::class, 'design_faktor_id','id');
    }

    public function design_faktor_komponen(){
        return $this->hasMany(DesignFaktorKomponen::class,'design_faktor_id');
    }
    public function komponen()
    {
        return $this->hasMany(DesignFaktorKomponen::class, 'design_faktor_id', 'id');
    }

    public function quisioner()
    {
        return $this->hasMany(QuisionerPertanyaan::class, 'design_faktor_id', 'id');
    }

    public function pertanyaan()
    {
        return $this->hasOne(QuisionerPertanyaan::class, 'design_faktor_id', 'id');
    }
}
