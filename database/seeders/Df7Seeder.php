<?php

namespace Database\Seeders;

use App\Models\DesignFaktorKomponen;
use App\Models\DesignFaktorMap;
use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Df7Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[[1.0,	2.0,	1.5,	4.0],
            [1.0,	1.0,	2.5,	3.0],
            [1.0,	3.0,	1.0,	3.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.5,	1.5,	2.5],
            [1.0,	1.0,	3.0,	3.0],
            [1.0,	1.0,	2.0,	2.0],
            [0.5,	1.0,	3.5,	4.0],
            [1.0,	1.0,	2.5,	3.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.0,	1.0,	1.5],
            [1.0,	1.0,	2.0,	2.5],
            [1.0,	2.0,	1.5,	2.0],
            [1.0,	2.5,	1.5,	2.0],
            [1.0,	1.5,	1.5,	2.0],
            [1.0,	2.5,	1.0,	3.0],
            [1.0,	2.0,	1.5,	3.0],
            [1.0,	1.5,	1.5,	2.5],
            [1.0,	1.0,	2.0,	2.5],
            [1.0,	1.0,	3.0,	3.0],
            [1.0,	1.0,	3.0,	3.0],
            [1.0,	2.5,	1.5,	2.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	2.5,	1.0,	2.0],
            [1.0,	1.0,	2.0,	2.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.5,	1.0,	2.0],
            [1.0,	1.0,	2.0,	2.0],
            [1.0,	3.5,	1.0,	3.0],
            [1.0,	3.0,	1.5,	3.0],
            [1.0,	3.0,	1.5,	3.5],
            [1.0,	3.0,	1.5,	3.5],
            [1.5,	2.5,	1.5,	3.5],
            [1.0,	1.0,	1.0,	2.5],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.0,	1.0,	2.0],
            [1.0,	1.0,	1.0,	1.5],
            [1.0,	1.0,	1.0,	2.0]];

        $domain=Domain::orderBy('urutan','ASC')->get();
        $komponen=DesignFaktorKomponen::join('design_faktor as df','df.id','=','design_faktor_id')->select(DB::raw('design_faktor_komponen.*'))->where('df.kode','DF7')->orderBy('urutan','ASC')->get();
        DesignFaktorMap::where('design_faktor_id',$komponen[0]->design_faktor_id)->forceDelete();
        foreach($domain as $key_dm=>$dm){
            foreach($komponen as $key_kom=>$kom){
                $groupValue=$data[$key_dm];
                $map=new DesignFaktorMap();
                $map->domain_id=$dm->id;
                $map->design_faktor_id=$kom->design_faktor_id;
                $map->design_faktor_komponen_id=$kom->id;
                $map->nilai=$groupValue[$key_kom];
                $map->save();
            }
        }
    }
}
