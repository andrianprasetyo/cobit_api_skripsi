<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisasiDivisiMapDF extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $table = 'organisasi_divisi_map_df';
    protected $keyType = 'string';
    protected $fillable = ['organisasi_divisi_id', 'design_faktor_id', 'assesment_id'];

    public function divisi()
    {
        return $this->belongsTo(OrganisasiDivisi::class, 'organisasi_divisi_id');
    }

    public function design_faktor()
    {
        return $this->belongsTo(DesignFaktor::class, 'design_faktor_id');
    }
}
