<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'domain';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'ket'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function assesmenthasil()
    {
        return $this->hasMany(AssesmentHasil::class,'domain_id');
    }

    public function assesmentcanvas()
    {
        return $this->belongsTo(AssesmentCanvas::class, 'id','domain_id');
    }
}
