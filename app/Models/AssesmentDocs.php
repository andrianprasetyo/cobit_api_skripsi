<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssesmentDocs extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $table = 'assesment_docs';
    protected $keyType = 'string';

    protected $fillable = ['assesment_id','name','file','version','parent_id','current'];

    protected $casts = [
        'file' => 'json'
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($item) {

    //         $latestItem = static::where('assesment_id', $item->assesment_id)
    //             ->latest()
    //             ->first();

    //         if ($latestItem) {
    //             $item->version = $latestItem->version + 1;
    //         } else {
    //             $item->version =1;
    //         }
    //     });
    // }

    public function getFileAttribute()
    {
        if (!is_null($this->attributes['file'])) {
            $file = json_decode($this->attributes['file']);
            $file->url = asset($file->path);
            return $file;
        }
        return null;
    }

    public function assesment()
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }
}
