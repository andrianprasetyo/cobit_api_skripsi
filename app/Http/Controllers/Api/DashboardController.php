<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\UserAssesment;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use JsonResponse;

    private $account;

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
            // $total_user_pic->where('assesment_id');
        }

        $data['total']=array(
            'responden'=> $total_responden->count(),
            'assesment' => $total_assesment->count(),
            'pic' => $total_user_pic->count(),
        );
        return $this->successResponse($data);
    }
}
