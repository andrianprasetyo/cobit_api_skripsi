<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganisasiDivisiJabatan extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'organisasi_divisi_jabatan';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'organisasi_divisi_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function divisi()
    {
        return $this->belongsTo(OrganisasiDivisi::class, 'organisasi_divisi_id');
    }
}
