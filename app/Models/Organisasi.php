<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organisasi extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'organisasi';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'assesment_id', 'deskripsi'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }

    public function divisi()
    {
        return $this->belongsTo(OrganisasiJabatan::class, 'organisasi_id')->where('jenis','divisi');
    }

    public function jabatan()
    {
        return $this->belongsTo(OrganisasiJabatan::class, 'organisasi_id')->where('jenis', 'jabatan');
    }
}
