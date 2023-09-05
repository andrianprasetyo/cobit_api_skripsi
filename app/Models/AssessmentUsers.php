<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AssessmentUsers extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Notifiable;

    public $incrementing = false;
    protected $table = 'assesment_users';
    protected $keyType = 'string';

    protected $fillable = ['assesment_id', 'nama','email','code','status','is_proses','divisi_id','jabatan_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }

    public function assesmentquisioner()
    {
        return $this->hasOne(AssessmentQuisioner::class, 'assesment_id','assesment_id');
    }

    public function assesmentquisionerhasil()
    {
        return $this->hasMany(QuisionerHasil::class, 'assesment_users_id', 'id');
    }

    public function divisi()
    {
        return $this->belongsTo(OrganisasiDivisi::class, 'divisi_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(OrganisasiDivisiJabatan::class, 'jabatan_id');
    }
}
