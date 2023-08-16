<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuisionerPertanyaan extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'quisioner_pertanyaan';
    protected $keyType = 'string';
    protected $fillable = ['pertanyaan', 'design_faktor_id', 'sorting','quisioner_id','quisioner_grup_jawaban_id'];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];

    public function design_faktor(){
        return $this->belongsTo(DesignFaktor::class,'design_faktor_id');
    }
    public function grup_jawaban(){
        return $this->belongsTo(QuisionerGrupJawaban::class,'quisioner_grup_jawaban_id');
    }
    public function quisioner()
    {
        return $this->belongsTo(Quisioner::class,'quisioner_id');
    }

    public function grup()
    {
        return $this->belongsTo(QuisionerGrupJawaban::class, 'quisioner_grup_jawaban_id');
    }
}
