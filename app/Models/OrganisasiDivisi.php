<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganisasiDivisi extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'organisasi_divisi';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'organisasi_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }

    public function jabatans()
    {
        return $this->hasMany(OrganisasiDivisiJabatan::class,'organisasi_divisi_id','id');
    }
}
