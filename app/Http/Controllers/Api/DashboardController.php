<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\UserAssesment;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use JsonResponse;

    private $account;
    private $assesment = null;

    public function __construct()
    {
        $this->account= auth()->user();
    }

    public function assesment()
    {
        $total_responden = AssessmentUsers::query();
        $total_assesment = Assesment::query();
        $total_user_pic= UserAssesment::query();
        if($this->account->internal){
            $total_user_pic->groupBy('users_id');
        }else{
            $total_user_pic->where('assesment_id',$this->account->assesment->assesment_id);
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
        $assesment_id=$request->id;
        $status=AssessmentUsers::select('status')->groupBy('status')->get();
        $categories=[];
        $series = [];
        $series_data = [];
        $now = date('Y');
        if(!$status->isEmpty())
        {
            foreach ($status as $_item_status) {
                // $categories[]=$_item_status->status;


                // $series_data[]=array(
                //     'name' => $_item_status->status,
                //     'data' => 0
                // );
            }
        }

        for ($imonth = 1; $imonth <= 12; $imonth++) {
            $name = Carbon::create()->month($imonth)->startOfMonth()->format('F');
            $categories[] = $name;

            // $qry_total_all = AssessmentUsers::where('assesment_id', $assesment_id)
            //     ->whereYear('created_at', $now)
            //     ->whereMonth('created_at', $imonth)
            //     ->count();

            $qry_total_done=AssessmentUsers::where('assesment_id',$assesment_id)
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth)
                ->where('status', 'done')
                ->count();

            $qry_total_invited = AssessmentUsers::where('assesment_id', $assesment_id)
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth)
                ->where('status', 'diundang')
                ->count();

            $qry_total_active = AssessmentUsers::where('assesment_id', $assesment_id)
                ->whereYear('created_at', $now)
                ->whereMonth('created_at', $imonth)
                ->where('status', 'active')
                ->count();

            $total_done[] = $qry_total_done;
            $total_invited[] = $qry_total_invited;
            $total_active[]=$qry_total_active;
            $total_all[] = $qry_total_done+ $qry_total_invited + $qry_total_active;
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
}
