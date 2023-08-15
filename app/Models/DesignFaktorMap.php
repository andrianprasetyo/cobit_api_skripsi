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
    protected $fillable = ['domain_id', 'design_faktor_id', 'growth','innovation','cost_leadership','client_service'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
