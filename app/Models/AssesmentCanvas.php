<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssesmentCanvas extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $table = 'assesment_canvas';
    protected $keyType = 'string';

    protected $fillable=[
        'assesment_id',
        'domain_id',
        'step2_init_value',
        'step2_value',
        'step3_init_value',
        'step3_value',
        'adjustment',
        'reason',
        'origin_capability_level',
        'suggest_capability_level',
        'aggreed_capability_level',
    ];

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
