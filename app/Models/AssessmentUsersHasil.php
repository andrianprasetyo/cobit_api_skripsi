<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentUsersHasil extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_users_hasil';
    protected $keyType = 'string';

    protected $fillable = ['design_faktor_id', 'assesment_user_id', 'domain_id', 'score', 'baseline_score', 'relative_importance'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
