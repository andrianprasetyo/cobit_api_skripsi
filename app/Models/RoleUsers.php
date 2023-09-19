<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoleUsers extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $table = 'roles_users';
    protected $keyType = 'string';
    protected $fillable = ['roles_id', 'users_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'roles_id');
    }
}
