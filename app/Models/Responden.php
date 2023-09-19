<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Responden extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Notifiable;

    public $incrementing = false;
    protected $table = 'responden';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'divisi', 'posisi', 'email', 'assesment_id','status'];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }
}
