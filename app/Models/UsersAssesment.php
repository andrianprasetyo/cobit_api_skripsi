<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersAssesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'users_assesments';
    protected $keyType = 'string';
    protected $fillable = ['users_id', 'assesment_id'];

    protected $hidden = [
        'deleted_at',
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
