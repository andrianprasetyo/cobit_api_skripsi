<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuisionerHasil extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'quisioner_hasil';
    protected $keyType = 'string';

    protected $fillable = ['quisioner_id', 'quisioner_pertanyaan_id','jawaban_id','assesment_users_id','bobot','design_faktor_komponen_id','responden_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];
}
