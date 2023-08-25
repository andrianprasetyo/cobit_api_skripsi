<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssesmentHasil extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $table = 'assesment_hasil';
    protected $keyType = 'string';


    public function designfaktor()
    {
        return $this->belongsTo(DesignFaktor::class,'design_faktor_id')->orderBy('urutan','ASC');
    }
}
