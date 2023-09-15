<?php

namespace App\Http\Controllers\Api;

use App\Exports\AnalisaGapExport;
use App\Exports\SummaryCapabilityAssesmentExport;
use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CapabilityAssesment\CapabilityAssesmentLevelResource;
use App\Http\Resources\CapabilityLevel\CapabilityLevelResource;
use App\Models\Assesment;
use App\Models\AssesmentDomain;
use App\Models\CapabilityAnswer;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentEvident;
use App\Models\CapabilityAssesmentOfi;
use App\Models\CapabilityAssesmentSubmited;
use App\Models\CapabilityLevel;
use App\Models\CapabilityTarget;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CapabilityAssesmentController extends Controller
{
    use JsonResponse;

    public function listX(Request $request)
    {
        // $target_ass=CapabilityTarget::where('id',)->where('assesment_id',$request->assesment_id)->first();

        $list=CapabilityLevel::with([
                'domain',
                'capabilityass',
                'capabilityass.capability_answer',
                'capabilityass.evident',
                'capabilityass.evident.docs'
            ])
            // ->whereRelation('capabilityass','assesment_id',$request->assesment_id)
            ->where('level',$request->level)
            ->where('domain_id', $request->domain_id)
            ->orderBy('urutan','asc');

        $_data_ass=$list->get();

        $data_ass=CapabilityAssesmentLevelResource::collection($_data_ass);
        $_total_bobot_level = [];
        $_total_bobot_answer=[];
        if(!$_data_ass->isEmpty())
        {
            foreach ($_data_ass as $_item_ass) {
                $_total_bobot_level[] = $_item_ass->bobot;
                if(isset($_item_ass->capabilityass->capability_answer->bobot))
                {
                    $_total_bobot_answer[]= (float)$_item_ass->capabilityass->capability_answer->bobot;
                }
            }
        }
        // $data = $this->paging($list, null, null, CapabilityAssesmentLevelResource::class);
        $data['list']= $data_ass;
        $data['answer']=CapabilityAnswer::orderBy('bobot','asc')->get();

        $total_bobot_answer = array_sum($_total_bobot_answer);
        $total_bobot_level=array_sum($_total_bobot_level);
        $total_result = $total_bobot_answer != 0?$total_bobot_answer/ $total_bobot_level:0;
        $data['total_bobot']=array(
            'level'=>$total_bobot_level,
            'answer' => $total_bobot_answer,
            'result' => round($total_result,2)
        );

        // $submited=CapabilityAssesmentSubmited::where('assesment_id',$request->assesment_id)
        //     ->where('domain_id',$request->domain_id)
        //     ->where('level',$request->level)
        //     ->where('submited',true)
        //     ->exists();

        // $data['submited']=$submited;
        return $this->successResponse($data);
    }

    public function list(Request $request)
    {
        // $target_id=$request->capability_target_id;
        $assesment_id = $request->assesment_id;
        $domain_id=$request->domain_id;
        $list_level = CapabilityLevel::with([
            'domain',
        ])
            // ->whereRelation('capabilityass','assesment_id',$request->assesment_id)
            ->where('level', $request->level)
            ->where('domain_id', $request->domain_id)
            ->orderBy('urutan', 'asc')
            ->get();

        $data_list=[];
        $_total_bobot_level = [];
        $_total_bobot_answer = [];

        if(!$list_level->isEmpty()){
            foreach ($list_level as $_item_level) {
                $level_item=$_item_level;
                $target=CapabilityAssesment::with([
                        'capability_answer',
                        'evident.docs',
                        'ofi.target'
                    ])
                    ->where('capability_level_id',$_item_level->id)
                    // ->where('capability_target_id', $target_id)
                    ->where('assesment_id',$assesment_id)
                    ->where('domain_id', $domain_id)
                    ->first();

                if(!$target)
                {
                    $target = array(
                        'id' => null,
                        'capability_level_id' => $_item_level->id,
                        'capability_answer_id' => null,
                        'note' => null,
                        'ofi' => null,
                    );
                }
                $level_item->subkode = $_item_level->domain->kode . '.' . $_item_level->kode;
                $level_item->capabilityass=$target;

                $_total_bobot_level[] = $_item_level->bobot;
                if (isset($target->capability_answer->bobot)) {
                    $_total_bobot_answer[] = (float) $target->capability_answer->bobot;
                }

                $data_list[]= $level_item;
            }
        }

        $data['answer'] = CapabilityAnswer::orderBy('bobot', 'asc')->get();
        $data['list']=$data_list;

        $total_bobot_answer = array_sum($_total_bobot_answer);
        $total_bobot_level = array_sum($_total_bobot_level);
        $total_result = $total_bobot_answer != 0 ? $total_bobot_answer / $total_bobot_level : 0;
        $data['total_bobot'] = array(
            'level' => $total_bobot_level,
            'answer' => $total_bobot_answer,
            'result' => round($total_result, 2)
        );
        return $this->successResponse($data);
    }

    public function createAnswer(Request $request)
    {
        // dd($_POST);
        $request->validate(
            [
                'assesment_id'=>'required',
                'domain_id' => 'required',
                'capability_assesment_id' => 'required|array',
                'capability_level_id' => 'required|array',
                'capability_answer_id' => 'required|array',
                // 'capability_target_id' => 'required',
            ],
            [
                'capability_assesment_id.required'=>'Capability Assesment ID harus di isi',
                'capability_level_id.required' => 'Capability Level ID harus di isi',
                'capability_answer_id.required' => 'Capability Answer ID harus di isi',
                'assesment_id.required' => 'Assesment ID harus di isi',
                'domain_id.required' => 'Domain ID harus di isi',
            ]
        );
        $capability_assesment = $request->capability_assesment_id;
        $capability_level_id = $request->capability_level_id;
        $capability_answer_id = $request->capability_answer_id;
        // $capability_target_id = $request->capability_target_id;

        $note = $request->note;
        $ofi = $request->ofi;
        $evidents = $request->evident;
        $ofi = $request->ofi;

        // $_deb=[];
        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($capability_assesment); $i++) {
                // $capabilityass = $_item_payload['capabilityass'];

                $capability_ass = new CapabilityAssesment();
                if ($capability_assesment[$i] != null) {
                    $capability_ass = CapabilityAssesment::find($capability_assesment[$i]);
                    if (!$capability_ass) {
                        return $this->errorResponse('Capbility asesment ID tidak ditemukan', 404);
                    }
                }
                // $capability_ass->capability_target_id = $capability_target_id[$i];
                $capability_ass->capability_level_id = $capability_level_id[$i];
                $capability_ass->capability_answer_id = $capability_answer_id[$i];
                $capability_ass->note = $note[$i];
                // $capability_ass->ofi = $ofi[$i];
                $capability_ass->assesment_id=$request->assesment_id;
                $capability_ass->domain_id = $request->domain_id;
                $capability_ass->save();

                if(isset($evidents[$i]) && count($evidents[$i]) > 0)
                {
                    $evident = $evidents[$i];
                    if (count($evident) > 0) {
                        CapabilityAssesmentEvident::where('capability_assesment_id', $capability_ass->id)->delete();
                        for ($r = 0; $r < count($evident); $r++) {
                            $_evident[] = array(
                                'id'=>Str::uuid(),
                                'capability_assesment_id' => $capability_ass->id,
                                'url' => isset($evident[$r]['url'])?$evident[$r]['url']:null,
                                'deskripsi'=>isset($evident[$r]['deskripsi'])?$evident[$r]['deskripsi']:null,
                                'media_repositories_id' => isset($evident[$r]['media_repositories_id']) ? $evident[$r]['media_repositories_id'] : null,
                            );
                        }
                        CapabilityAssesmentEvident::insert($_evident);
                    }
                }

                if(isset($ofi[$i]) && count($ofi[$i]) > 0)
                {
                    if (count($ofi) > 0) {
                        CapabilityAssesmentOfi::where('capability_assesment_id', $capability_ass->id)->delete();
                        $_ofi = [];
                        $ofi_item=$ofi[$i];
                        for ($o = 0; $o < count($ofi); $o++) {
                            $new_ofi=new CapabilityAssesmentOfi();
                            $new_ofi->capability_assesment_id=$capability_ass->id;
                            $new_ofi->domain_id = $request->domain_id;
                            $new_ofi->ofi= isset($ofi_item[$o]['ofi']) ? $ofi_item[$o]['ofi'] : null;
                            $new_ofi->capability_target_id = isset($ofi_item[$o]['capability_target_id']) ? $ofi_item[$o]['capability_target_id'] : null;
                            $new_ofi->save();

                            // $_ofi[] = array(
                            //     'id' => Str::uuid(),
                            //     'capability_assesment_id' => $capability_ass->id,
                            //     'ofi' => isset($ofi_item[$o]['ofi']) ? $ofi_item[$o]['ofi'] : null,
                            //     'capability_target_id' => isset($ofi_item[$o]['capability_target_id']) ? $ofi_item[$o]['capability_target_id'] : null,
                            //     'domain_id' => $request->domain_id,
                            //     'created_at' => Carbon::now()->toDateTimeString(),
                            //     'updated_at' => Carbon::now()->toDateTimeString()
                            // );
                        }
                        // CapabilityAssesmentOfi::insert($_ofi);
                        // $_deb[]=$_ofi;
                    }
                }
            }

            // CapabilityAssesmentSubmited::firstOrCreate([
            //     'assesment_id'=>$request->assesment_id,
            //     'domain_id' => $request->domain_id,
            //     'level' => $request->level,
            //     'submited'=>true
            // ]);

            DB::commit();
            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function uploadEvident(Request $request,$id)
    {
        $tipe=$request->tipe;
        $docs = $request->docs;
        $path = config('filesystems.path.evident');

        $payload=[];
        for ($r=0; $r < count($tipe); $r++) {
            $url = null;
            $files = null;
            if($tipe[$r] == 'file'){
                $_doc= $docs[$r];
                $filename = $_doc->hashName();
                $_doc->storeAs($path, $filename);
                $files = CobitHelper::Media($filename, $path, $_doc);
            }else{
                $url= $docs[$r];
            }
            $payload[]=array(
                'capability_assesment_id'=>$id,
                'tipe'=>$tipe[$r],
                'files'=>$files,
                'url' => $url,
            );

            $_new=new CapabilityAssesmentEvident();
            $_new->capability_assesment_id=$id;
            $_new->tipe= $tipe[$r];
            $_new->files=$files;
            $_new->url = $url;
            $_new->save();
        }

        return $this->successResponse($payload);
    }

    public function kalkukasiDomainByLevelBACKUP(Request $request)
    {
        $list_level=CapabilityLevel::select('level')
            ->where('domain_id', $request->domain_id)
            ->groupBy('level')
            ->orderBy('level','ASC')
            ->get();


        $result = [];
        $total=0;
        if(!$list_level->isEmpty())
        {
            $_total=[];
            foreach ($list_level as $_item_level) {
                $_sum_bobot = DB::table('capability_assesment')
                    ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                    ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                    ->join('assesment_domain','capability_level.domain_id','=','assesment_domain.domain_id')
                    ->where('assesment_domain.assesment_id', $request->asesment_id)
                    ->where('capability_level.level', $_item_level->level)
                    ->where('capability_level.domain_id', $request->domain_id)
                    ->whereNull('capability_assesment.deleted_at')
                    ->whereNull('capability_level.deleted_at')
                    ->whereNull('capability_answer.deleted_at')
                    ->whereNull('assesment_domain.deleted_at')
                    ->select(
                        // 'capability_assesment.id',
                        // 'capability_assesment.capability_level_id',
                        // 'capability_level.bobot as bobot_level',
                        'capability_answer.bobot as bobot_answer',
                    )
                    ->sum('capability_answer.bobot');

                $result[]=array(
                    'level'=>(float)$_item_level->level,
                    'kompilasi' => (float)$_sum_bobot,
                );
                $_total[]=(float)$_sum_bobot;
            }
            $total=round(array_sum($_total),2) + 1;
        }


        $data['list'] = $result;
        $data['total']=$total;
        return $this->successResponse($data);
    }

    public function kalkukasiDomainByLevel(Request $request)
    {

        $domain_id=$request->domain_id;
        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        $domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->select('domain.id', 'domain.kode', 'domain.ket')
            ->where('domain.id', $domain_id)
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->whereNull('domain.deleted_at')
            // ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->first();


        $_list_level = [];
        $_total_all = [];
        if($domain)
        {

            $list_levels = DB::table('capability_level')
                ->where('domain_id', $domain->id)
                ->whereNull('capability_level.deleted_at')
                ->select('level')
                ->groupBy('level')
                ->orderBy('level', 'asc')
                ->get();

            if(!$list_levels->isEmpty())
            {
                foreach ($list_levels as $_item_level)
                {
                    $_level = DB::table('capability_assesment')
                        ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                        ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                        ->where('capability_level.domain_id', $domain->id)
                        ->where('capability_level.level', $_item_level->level)
                        ->whereNull('capability_assesment.deleted_at')
                        ->whereNull('capability_level.deleted_at')
                        ->whereNull('capability_answer.deleted_at')
                        ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                        ->first();

                    $_bobot = DB::table('capability_level')
                        ->where('domain_id', $domain->id)
                        ->where('level', $_item_level->level)
                        ->whereNull('capability_level.deleted_at')
                        ->select(DB::raw("SUM(bobot) as bobot_level"))
                        ->first();

                    $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : 0;
                    $_bobot_level = $_bobot->bobot_level ? $_bobot->bobot_level : 0;

                    $_total_compilance = 0;
                    if ($_total_sum_compilance != 0) {
                        $_total_compilance = round($_total_sum_compilance / $_bobot_level, 2);
                    }

                    $_total_all[] = $_total_compilance;
                    $_list_level[] = array(
                        'level' => $_item_level->level,
                        'compliance' => $_total_compilance,
                    );
                }
            }
        }
        $data['list'] = $_list_level;
        $data['total'] = array_sum($_total_all);
        return $this->successResponse($data);
    }

    public function summaryAssesment(Request $request)
    {
        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        // $list_level = CapabilityLevel::select('level')
        //     ->groupBy('level')
        //     ->orderBy('level', 'asc')
        //     ->get();

        $cap_answer = CapabilityAnswer::all();
        $answer_val=[];
        if(!$cap_answer->isEmpty())
        {
            foreach ($cap_answer as $_item_cap) {
                $answer_val[$_item_cap->label]=(float)$_item_cap->bobot;
            }
        }
        // return $this->successResponse($answer_val);

        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->select('domain.id', 'domain.kode','domain.ket')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at')
            ->orderBy('domain.urutan', 'asc')
            ->get();


        $list = [];
        $daftar_level=[];
        if (!$list_domain->isEmpty()) {
            foreach ($list_domain as $_item_domain) {

                $_list_level = [];
                $_total_all=[];

                $list_levels = DB::table('capability_level')
                    ->where('domain_id', $_item_domain->id)
                    ->whereNull('capability_level.deleted_at')
                    ->select('level')
                    ->groupBy('level')
                    ->orderBy('level', 'asc')
                    ->get();

                if(!$list_levels->isEmpty())
                {
                    foreach ($list_levels as $_item_level) {

                        $daftar_level[]=$_item_level->level;
                        $_level = DB::table('capability_assesment')
                            ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                            ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                            ->where('capability_level.domain_id', $_item_domain->id)
                            ->where('capability_level.level', $_item_level->level)
                            ->whereNull('capability_assesment.deleted_at')
                            ->whereNull('capability_level.deleted_at')
                            ->whereNull('capability_answer.deleted_at')
                            ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                            ->first();

                        $_bobot = DB::table('capability_level')
                            ->where('domain_id', $_item_domain->id)
                            ->where('level', $_item_level->level)
                            ->whereNull('capability_level.deleted_at')
                            ->select(DB::raw("SUM(bobot) as bobot_level"))
                            ->first();



                        // $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : 0;
                        // $_total_sum_compilance = null;
                        // if($_level->compilance != null)
                        // {
                        //     $_total_sum_compilance = $_level->compilance;
                        // }
                        // $_total_sum_compilance = (float) $_level->compilance;
                        // $_bobot_level=$_bobot->bobot_level?$_bobot->bobot_level:0;

                        // $_total_compilance=0;
                        // if($_total_sum_compilance != null)
                        // {
                        //     $_total_compilance = $_total_sum_compilance !=0?round($_total_sum_compilance / $_bobot->bobot_level, 2):0;
                        // }

                        $sts = null;

                        if($_level->compilance != null){

                            $_total_sum_compilance = $_level->compilance;
                            $_total_compilance = $_total_sum_compilance != 0 ? round($_total_sum_compilance / $_bobot->bobot_level, 2) : 0;

                            if($_total_compilance  == $answer_val['N/A']){
                                $sts='N/A';
                            }else if($_total_compilance > $answer_val['N/A'] && $_total_compilance < $answer_val['N']){
                                if($sts != 'N/A'){
                                    $sts='N';
                                }
                            } else if ($_total_compilance >= $answer_val['N'] && $_total_compilance < $answer_val['P']){
                                if ($sts != 'N/A') {
                                    $sts = 'P';
                                }
                            } else if ($_total_compilance >= $answer_val['P'] && $_total_compilance < $answer_val['L']){
                                if ($sts != 'N/A') {
                                    $sts = 'L';
                                }
                            } else if ($_total_compilance >= $answer_val['L'] && $_total_compilance <= $answer_val['F']){
                                if ($sts != 'N/A') {
                                    $sts = 'F';
                                }
                            }

                            $_total_all[] = $_total_compilance;

                            $_list_level[] = array(
                                'level' => $_item_level->level,
                                'total_compilance' => $_total_compilance,
                                'label' => $sts,
                                // '_total_sum_compilance' => $_total_sum_compilance
                            );
                        }

                        // if($_level->compilance != null)
                        // {

                        //     if ($_total_compilance > 0 && $_total_compilance < 0.15) {
                        //         $sts = 'N';
                        //     } else if ($_total_compilance > 0.15 && $_total_compilance <= 0.50) {
                        //         $sts = 'P';
                        //     } else if ($_total_compilance > 0.50 && $_total_compilance <= 0.85) {
                        //         $sts = 'L';
                        //     } else if ($_total_compilance > 0.85 && $_total_compilance <= 1) {
                        //         $sts = 'F';
                        //     } else {
                        //         $sts = 'N/A';
                        //     }
                        // }


                    }
                }
                $list[] = array(
                    'id' => $_item_domain->id,
                    'kode' => $_item_domain->kode,
                    'ket' => $_item_domain->ket,
                    'level' => $_list_level,
                    'total' => array_sum($_total_all)
                );
            }
        }

        // $data = $this->paging($list);
        $data['list'] = $list;
        $data['level'] = collect($daftar_level)->unique()->values();
        return $this->successResponse($data);
    }

    public function downloadReportSumCapabilityAss(Request $request)
    {
        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }

        // $list_level = CapabilityLevel::select('level')
        //     ->groupBy('level')
        //     ->orderBy('level', 'asc')
        //     ->get();

        $cap_answer = CapabilityAnswer::all();
        $answer_val = [];
        if (!$cap_answer->isEmpty()) {
            foreach ($cap_answer as $_item_cap) {
                $answer_val[$_item_cap->label] = (float) $_item_cap->bobot;
            }
        }
        // return $this->successResponse($answer_val);

        $list_domain = DB::table('assesment_canvas')
            ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
            ->select('domain.id', 'domain.kode', 'domain.ket')
            ->where('assesment_canvas.assesment_id', $assesment->id)
            ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
            ->whereNull('domain.deleted_at')
            ->orderBy('domain.urutan', 'asc')
            ->get();


        $list = [];
        $daftar_level = [];
        if (!$list_domain->isEmpty()) {
            foreach ($list_domain as $_item_domain) {

                $_list_level = [];
                $_total_all = [];

                $list_levels = DB::table('capability_level')
                    ->where('domain_id', $_item_domain->id)
                    ->whereNull('capability_level.deleted_at')
                    ->select('level')
                    ->groupBy('level')
                    ->orderBy('level', 'asc')
                    ->get();

                if (!$list_levels->isEmpty()) {
                    foreach ($list_levels as $_item_level) {

                        $daftar_level[] = $_item_level->level;
                        $_level = DB::table('capability_assesment')
                            ->join('capability_level', 'capability_assesment.capability_level_id', '=', 'capability_level.id')
                            ->join('capability_answer', 'capability_assesment.capability_answer_id', '=', 'capability_answer.id')
                            ->where('capability_level.domain_id', $_item_domain->id)
                            ->where('capability_level.level', $_item_level->level)
                            ->whereNull('capability_assesment.deleted_at')
                            ->whereNull('capability_level.deleted_at')
                            ->whereNull('capability_answer.deleted_at')
                            ->select(DB::raw("SUM(capability_answer.bobot) as compilance"))
                            ->first();

                        $_bobot = DB::table('capability_level')
                            ->where('domain_id', $_item_domain->id)
                            ->where('level', $_item_level->level)
                            ->whereNull('capability_level.deleted_at')
                            ->select(DB::raw("SUM(bobot) as bobot_level"))
                            ->first();



                        // $_total_sum_compilance = $_level->compilance != null ? (float) $_level->compilance : 0;
                        // $_total_sum_compilance = null;
                        // if($_level->compilance != null)
                        // {
                        //     $_total_sum_compilance = $_level->compilance;
                        // }
                        // $_total_sum_compilance = (float) $_level->compilance;
                        // $_bobot_level=$_bobot->bobot_level?$_bobot->bobot_level:0;

                        // $_total_compilance=0;
                        // if($_total_sum_compilance != null)
                        // {
                        //     $_total_compilance = $_total_sum_compilance !=0?round($_total_sum_compilance / $_bobot->bobot_level, 2):0;
                        // }

                        $sts = null;

                        if ($_level->compilance != null) {

                            $_total_sum_compilance = $_level->compilance;
                            $_total_compilance = $_total_sum_compilance != 0 ? round($_total_sum_compilance / $_bobot->bobot_level, 2) : 0;

                            if ($_total_compilance == $answer_val['N/A']) {
                                $sts = 'N/A';
                            } else if ($_total_compilance > $answer_val['N/A'] && $_total_compilance < $answer_val['N']) {
                                if ($sts != 'N/A') {
                                    $sts = 'N';
                                }
                            } else if ($_total_compilance >= $answer_val['N'] && $_total_compilance < $answer_val['P']) {
                                if ($sts != 'N/A') {
                                    $sts = 'P';
                                }
                            } else if ($_total_compilance >= $answer_val['P'] && $_total_compilance < $answer_val['L']) {
                                if ($sts != 'N/A') {
                                    $sts = 'L';
                                }
                            } else if ($_total_compilance >= $answer_val['L'] && $_total_compilance <= $answer_val['F']) {
                                if ($sts != 'N/A') {
                                    $sts = 'F';
                                }
                            }

                            $_total_all[] = $_total_compilance;

                            $_list_level[] = array(
                                'level' => $_item_level->level,
                                'total_compilance' => $_total_compilance,
                                'label' => $sts,
                                // '_total_sum_compilance' => $_total_sum_compilance
                            );
                        }

                        // if($_level->compilance != null)
                        // {

                        //     if ($_total_compilance > 0 && $_total_compilance < 0.15) {
                        //         $sts = 'N';
                        //     } else if ($_total_compilance > 0.15 && $_total_compilance <= 0.50) {
                        //         $sts = 'P';
                        //     } else if ($_total_compilance > 0.50 && $_total_compilance <= 0.85) {
                        //         $sts = 'L';
                        //     } else if ($_total_compilance > 0.85 && $_total_compilance <= 1) {
                        //         $sts = 'F';
                        //     } else {
                        //         $sts = 'N/A';
                        //     }
                        // }


                    }
                }
                $list[] = array(
                    'id' => $_item_domain->id,
                    'kode' => $_item_domain->kode,
                    'ket' => $_item_domain->ket,
                    'level' => $_list_level,
                    'total' => array_sum($_total_all)
                );
            }
        }

        // $data = $this->paging($list);
        $data['list'] = $list;
        $data['level'] = collect($daftar_level)->unique()->values();

        return Excel::download(new SummaryCapabilityAssesmentExport($data), 'report-summary-capabbility-assesment.xlsx');
    }

    public function detailOfi(Request $request)
    {
        $data = CapabilityAssesmentOfi::find($request->id);

        return $this->successResponse($data);
    }
}
