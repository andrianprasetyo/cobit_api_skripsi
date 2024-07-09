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
    protected $fillable = ['nama', 'deskripsi','organisasi_id','status','deskripsi','users_id','start_date','end_date','start_date_quisioner','end_date_quisioner','docs'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'docs' => 'json'
    ];

    public function scopeExpire(Builder $query):void
    {
        if (auth()->user()->assesment != null) {
            $user_id = auth()->user()->id;
            $query->whereIn('id', function ($q) use ($user_id) {
                $q->select('assesment_id')
                    ->from('users_assesment')
                    ->where('users_id', $user_id);
            })
            ->where('end_date', '>', date('Y-m-d'));
        }
    }

    public function getDocsAttribute()
    {
        if (!is_null($this->attributes['docs'])) {
            $file = json_decode($this->attributes['docs']);
            $file->url = asset($file->path);
            return $file;
        }
        return null;
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class,'users_id');
    }

    public function users()
    {
        return $this->hasMany(AssessmentUsers::class,'assesment_id','id');
    }

    public function docs()
    {
        // return $this->hasMany(AssesmentDocs::class, 'assesment_id', 'id')->orderBy('created_at','desc');
        return $this->belongsTo(AssesmentDocs::class, 'assesment_id', 'id')->latest();
    }

    // jangan digunakan dulu
    public function allpic()
    {
        return $this->hasMany(User::class, 'pic_assesment_id', 'id');
    }
}
