<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignFaktorMap extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'design_faktor_map';
    protected $keyType = 'string';
    protected $fillable = ['domain_id', 'design_faktor_id', 'design_faktor_komponen_id','nilai'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function domain()
    {
        $this->belongsTo(Domain::class,'domain_id');
    }

    public function deisgnfaktor()
    {
        $this->belongsTo(DesignFaktor::class, 'design_faktor_id');
    }

    public function deisgnfaktorkomponen()
    {
        $this->belongsTo(DesignFaktorKomponen::class, 'design_faktor_komponen_id');
    }
}
