<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssesmentDomain extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_domain';
    protected $keyType = 'string';
    protected $fillable = ['assesment_id', 'domain_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
