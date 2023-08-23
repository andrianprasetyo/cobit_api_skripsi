<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi','organisasi_id','status','deskripsi','tahun'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            UsersAssesment::class,
            User::class,
            'id',
            'assesment_id',
            'id',
            'id'
        );
    }
}
