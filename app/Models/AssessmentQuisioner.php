<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentQuisioner extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_quisioner';
    protected $keyType = 'string';

    protected $fillable = ['assesment_id', 'quisioner_id', 'organisasi_id', 'allow'];


    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }

    public function auisioner()
    {
        return $this->belongsTo(Quisioner::class, 'quisioner_id');
    }
}
