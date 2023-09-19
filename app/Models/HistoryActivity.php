<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryActivity extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'history_activities';
    protected $keyType = 'string';
    protected $fillable = ['action', 'before', 'after','created_by','pk','module','path','method','create_by_role','description'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'before' => 'json',
        'after' => 'json'
    ];
}
