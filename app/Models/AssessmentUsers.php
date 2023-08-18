<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentUsers extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_users';
    protected $keyType = 'string';

    protected $fillable = ['assesment_id', 'nama','email','divisi','jabatan','code','status','pic'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
