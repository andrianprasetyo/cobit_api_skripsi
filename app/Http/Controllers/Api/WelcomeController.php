<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Models\Assesment;
use App\Models\AssessmentUsers;
use App\Models\AssessmentUsersHasil;
use App\Models\DesignFaktor;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Traits\JsonResponse;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    use JsonResponse;

    public function halo()
    {
        return $this->successResponse();
    }
    public function prosesHasilQuisioner(){
        ini_set('max_execution_time', 300);
        CobitHelper::getQuisionerHasil('99f92060-d210-41dd-87fc-5686e418eb48');
//        $users=AssessmentUsers::where('is_proses',null)->get();
//        foreach($users as $user){
//            CobitHelper::getQuisionerHasil($user->id);
//        }
    }
    public function prosesHasilCanvas(){
        ini_set('max_execution_time', 300);
        $assesment=Assesment::get();
        foreach($assesment as $as){
            CobitHelper::setAssesmentHasilAvg($as->id);
            CobitHelper::assesmentDfWeight($as->id);
            CobitHelper::setCanvasStep2Value($as->id);
            CobitHelper::setCanvasStep3Value($as->id);
            CobitHelper::updateCanvasAdjust($as->id);
        }
    }

}
