<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Roles extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'roles';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'code','deskripsi','aktif'];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        // 'created_at'
    ];

    public function usersrole()
    {
        return $this->hasMany(RoleUsers::class,'roles_id','id');
    }
}
