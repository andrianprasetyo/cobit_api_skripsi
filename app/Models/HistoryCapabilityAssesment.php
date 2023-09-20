<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryCapabilityAssesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'history_capability_assesments';
    protected $keyType = 'string';

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'before' => 'json',
        'after' => 'json'
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
