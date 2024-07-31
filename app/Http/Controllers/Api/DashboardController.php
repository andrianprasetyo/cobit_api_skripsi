<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssesmentDomain;
use App\Models\AssessmentUsers;
use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use App\Models\Domain;
use App\Models\UserAssesment;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use JsonResponse;

    // private $account;
    // private $assesment = null;

    // public function __construct()
    // {
    //     $this->account= auth()->user();
    // }

    public function assesment()
    {
        $total_responden = AssessmentUsers::query();
        $total_assesment =0;
        $total_user_pic= UserAssesment::query();
        if($this->account->internal){
            $total_assesment = Assesment::query();
            $total_user_pic->groupBy('users_id');
        }else{
            $total_user_pic->where('assesment_id',$this->account->assesment->assesment_id);
            $total_assesment = UserAssesment::where('users_id', $this->account->id);
            $total_responden->where('assesment_id',$this->account->assesment->assesment_id);
        }

        $data['total']=array(
            'responden'=> $total_responden->count(),
            'assesment' => $total_assesment->count(),
            'pic' => $total_user_pic->count(),
        );
        return $this->successResponse($data);
    }

    public function assesmentChart(Request $request)
    {
        $assesment_id = $request->id;
        $now=$request->get('tahun', date('Y'));
        $categories=[];
        $series = [];

        for ($imonth = 1; $imonth <= 12; $imonth++) {
            $name = Carbon::create()->month($imonth)->startOfMonth()->format('F');
            $categories[] = $name;

            $qry_total_done=AssessmentUsers::where('status', 'done')
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth);

            $qry_total_invited = AssessmentUsers::where('status', 'diundang')
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth);

            $qry_total_active = AssessmentUsers::where('status', 'active')
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth);


            if(!$this->account->internal)
            {
                $account=$this->account;
                $qry_total_done->whereIn('assesment_id', function ($q) use ($account) {
                    $q->select('assesment_id')
                        ->from('users_assesment')
                        ->where('users_id', $account->id);
                });
                $qry_total_invited->whereIn('assesment_id', function ($q) use ($account) {
                    $q->select('assesment_id')
                        ->from('users_assesment')
                        ->where('users_id', $account->id);
                });
                $qry_total_active->whereIn('assesment_id', function ($q) use ($account) {
                    $q->select('assesment_id')
                        ->from('users_assesment')
                        ->where('users_id', $account->id);
                });
            }

            if($request->filled('id'))
            {
                $qry_total_done->where('assesment_id', $assesment_id);
                $qry_total_invited->where('assesment_id', $assesment_id);
                $qry_total_active->where('assesment_id', $assesment_id);
            }

            $_total_done=$qry_total_done->count();
            $_total_invited = $qry_total_invited->count();
            $_total_active=$qry_total_active->count();

            $total_done[] = $_total_done;
            $total_invited[] = $_total_invited;
            $total_active[]=$_total_active;
            $total_all[] = $_total_done+ $_total_invited + $_total_active;
        }

        $series= array(
            [
                'name' => 'All',
                'data' => $total_all
            ],
            [
                'name' => 'Selesai',
                'data' => $total_done
            ],
            [
                'name' => 'Diundang',
                'data' => $total_invited
            ],
            [
                'name' => 'Aktif',
                'data' => $total_active
            ]
        );

        $data['categories'] = $categories;
        $data['series'] = $series;
        return $this->successResponse($data);
    }

    public function dashboardByAssesment(Request $request)
    {
        $assesment_id=$request->assesment_id;
        $assesment = Assesment::find($request->assesment_id);
        if (!$assesment) {
            return $this->errorResponse('Assesment tidak terdafter', 404);
        }
        $responden = AssessmentUsers::where('assesment_id',$assesment_id)->count();
        // $gamo = AssesmentDomain::where('assesment_id', $assesment_id)->count();
        // $gamo = DB::table('assesment_canvas')
        //     ->join('domain', 'assesment_canvas.domain_id', '=', 'domain.id')
        //     ->select('domain.id', 'domain.kode')
        //     ->where('assesment_canvas.assesment_id', $assesment_id)
        //     ->where('assesment_canvas.aggreed_capability_level', '>=', $assesment->minimum_target)
        //     ->whereNull('domain.deleted_at')
        //     ->count();

        $gamo = CapabilityTargetLevel::join('capability_target', 'capability_target_level.capability_target_id', '=', 'capability_target.id')
            ->join('domain', 'capability_target_level.domain_id', '=', 'domain.id')
            ->where('capability_target.assesment_id', $assesment_id)
            ->where('capability_target_level.target', '>=', $assesment->minimum_target)
            ->select('domain.id')
            ->groupBy('domain.id')
            ->count();

        $capability_taget = CapabilityTarget::where('assesment_id', $assesment_id)->count();

        $data['responden'] = $responden;
        $data['gamo'] = $gamo;
        $data['capability_taget']=$capability_taget;
        return $this->successResponse($data);
    }
}
