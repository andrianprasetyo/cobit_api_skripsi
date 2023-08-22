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

    protected $fillable = ['assesment_id', 'nama','email','divisi','jabatan','code','status','is_proses'];

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
}
