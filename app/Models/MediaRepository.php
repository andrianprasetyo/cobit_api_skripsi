<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaRepository extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'media_repositories';
    protected $keyType = 'string';
    protected $fillable = ['assesment_id', 'upload_by','docs','deskripsi'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'docs' => 'json'
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'upload_by');
    }

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }
}
