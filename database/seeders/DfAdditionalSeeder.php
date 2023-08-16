<?php

namespace Database\Seeders;

use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use App\Models\DesignFaktorMap;
use App\Models\DesignFaktorMapAdditional;
use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DfAdditionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain=[
            'AG01',
            'AG02',
            'AG03',
            'AG04',
            'AG05',
            'AG06',
            'AG07',
            'AG08',
            'AG09',
            'AG10',
            'AG11',
            'AG12',
            'AG13',
        ];
        $df=DesignFaktor::where('kode', 'DF2')->first()->id;
        DesignFaktorMapAdditional::getQuery()->delete();
        foreach($domain as $key=>$val){
            /*dd($df);*/
            $n=new DesignFaktorMapAdditional();
            $n->domain=$val;
            $n->design_faktor_id=$df;
            $n->urutan=$key;
            $n->save();
        }

        $data=[[0,	0,	1,	0,	2,	2,	0,	2,	2,	0,	0,	0,	2],
            [1,	2,	0,	0,	0,	0,	2,	0,	0,	0,	1,	0,	0],
            [2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	2,	0,	1],
            [0,	0,	0,	2,	0,	0,	0,	0,	0,	2,	0,	0,	0],
            [0,	0,	1,	0,	1,	1,	0,	2,	1,	0,	0,	1,	0],
            [0,	1,	0,	0,	1,	0,	2,	0,	0,	0,	0,	0,	0],
            [0,	0,	0,	2,	0,	0,	0,	0,	0,	2,	0,	0,	0],
            [0,	0,	1,	0,	1,	1,	0,	1,	1,	0,	0,	0,	0],
            [0,	0,	1,	2,	0,	0,	0,	0,	1,	1,	0,	0,	0],
            [0,	0,	0,	0,	0,	0,	0,	1,	0,	0,	0,	2,	0],
            [1,	0,	0,	0,	0,	0,	0,	0,	0,	0,	2,	0,	0],
            [0,	0,	2,	0,	1,	1,	0,	2,	2,	0,	0,	0,	1],
            [0,	0,	0,	0,	0,	1,	0,	1,	1,	0,	0,	0,	2]];
        $komponen=DesignFaktorKomponen::join('design_faktor as df','df.id','=','design_faktor_id')->select(DB::raw('design_faktor_komponen.*'))->where('df.kode','DF2')->orderBy('urutan','ASC')->get();
        $designFaktor=DesignFaktor::where('kode', 'DF2')->first()->id;
        $designFaktorMapAdditional=DesignFaktorMapAdditional::where('design_faktor_id',$designFaktor)->orderBy('urutan','ASC')->get();
        DesignFaktorMap::where('design_faktor_id',$designFaktor)->forceDelete();
        foreach($komponen as $key=>$kom){
            foreach($designFaktorMapAdditional as $key_dom=>$dom){
                $groupValue=$data[$key];
                $n=new DesignFaktorMap();
//                $n->domain_id=$dm->id;
                $n->design_faktor_id=$kom->design_faktor_id;
                $n->design_faktor_komponen_id=$kom->id;
                $n->nilai=$groupValue[$key_dom];
                $n->design_faktor_map_additional_id=$dom->id;
                $n->save();
            }
        }

        $data=[[2,	0,	1,	0,	0,	1,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	1,	1,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	1,	0,	1,	1,	2,	1],
            [1,	0,	2,	0,	0,	1,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	2,	1,	1,	0,	0,	0,	0,	0,	1,	1,	0,	0,	0,	0,	0,	1,	1,	1,	2,	1,	0,	1,	0,	1],
            [2,	2,	0,	1,	0,	2,	1,	1,	1,	2,	1,	1,	1,	0,	0,	1,	0,	0,	0,	2,	1,	1,	0,	2,	0,	0,	1,	0,	0,	2,	0,	0,	0,	0,	0,	0,	1,	0,	0,	0],
            [0,	0,	0,	0,	1,	0,	0,	0,	0,	0,	2,	0,	0,	0,	0,	1,	0,	0,	1,	0,	0,	0,	0,	0,	0,	0,	0,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	1,	0,	1],
            [0,	1,	0,	1,	0,	1,	1,	1,	0,	2,	0,	1,	2,	2,	2,	1,	0,	0,	0,	0,	2,	2,	2,	1,	1,	0,	0,	0,	1,	1,	2,	2,	2,	2,	1,	1,	2,	1,	0,	1],
            [0,	1,	0,	1,	0,	0,	1,	2,	2,	1,	0,	0,	2,	0,	1,	0,	0,	0,	0,	1,	2,	2,	0,	1,	2,	2,	1,	0,	0,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0],
            [0,	0,	2,	0,	0,	1,	0,	1,	0,	0,	0,	0,	0,	0,	0,	0,	2,	2,	1,	0,	0,	0,	1,	0,	0,	0,	0,	0,	2,	0,	0,	1,	1,	2,	2,	1,	0,	1,	0,	1],
            [1,	1,	0,	1,	0,	1,	2,	2,	1,	1,	0,	0,	1,	1,	0,	0,	0,	0,	0,	1,	1,	1,	0,	2,	1,	0,	1,	0,	0,	0,	1,	0,	0,	0,	0,	2,	0,	0,	0,	0],
            [0,	0,	0,	2,	0,	1,	0,	0,	0,	1,	2,	1,	1,	0,	1,	2,	0,	0,	0,	2,	2,	2,	1,	2,	0,	1,	1,	0,	0,	2,	0,	0,	0,	0,	0,	0,	1,	1,	0,	0],
            [0,	0,	0,	0,	2,	1,	0,	0,	0,	0,	1,	0,	0,	0,	0,	2,	0,	0,	2,	0,	0,	0,	0,	0,	0,	0,	0,	1,	0,	0,	0,	0,	0,	0,	0,	0,	2,	1,	0,	1],
            [1,	0,	1,	0,	1,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	1,	1,	1,	2,	1,	2],
            [0,	0,	0,	1,	0,	0,	1,	0,	1,	0,	0,	2,	2,	0,	0,	0,	0,	0,	0,	0,	1,	0,	0,	1,	0,	0,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0],
            [0,	1,	0,	0,	0,	0,	1,	0,	2,	0,	0,	2,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	2,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0]];

        $designFaktorMapAdditional=DesignFaktorMapAdditional::where('design_faktor_id',$designFaktor)->orderBy('urutan','ASC')->get();
        $domain=Domain::orderBy('urutan','ASC')->get();
        foreach($designFaktorMapAdditional as $keymap=>$dfm){
            foreach($domain as $keydom=>$dom){
                $groupValue=$data[$keymap];
                $n=new DesignFaktorMap();
                $n->domain_id=$dom->id;
                $n->design_faktor_id=$designFaktor;
//                $n->design_faktor_komponen_id=$kom->id;
                $n->nilai=$groupValue[$keydom];
                $n->design_faktor_map_additional_id=$dfm->id;
                $n->save();
            }
        }
    }
}
