<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Assesment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment';
    protected $keyType = 'string';
    protected $fillable = ['nama', 'deskripsi','organisasi_id','status','deskripsi','users_id','start_date','end_date','start_date_quisioner','end_date_quisioner'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function scopeExpire(Builder $query):void
    {
        if (Auth::check() && auth()->user()->assesment != null) {
            $user_id = auth()->user()->id;
            $query->whereIn('id', function ($q) use ($user_id) {
                $q->select('assesment_id')
                    ->from('users_assesment')
                    ->where('users_id', $user_id);
            })
            ->where('end_date', '>', date('Y-m-d'));
        }
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class,'users_id');
    }
}
