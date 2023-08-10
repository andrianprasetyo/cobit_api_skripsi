<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleUsers extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'roles_users';
    protected $keyType = 'string';
    protected $fillable = ['roles_id', 'users_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
