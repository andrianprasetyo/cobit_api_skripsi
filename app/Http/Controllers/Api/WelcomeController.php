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
        $users=AssessmentUsers::where('is_proses',null)->get();
        foreach($users as $user){
            CobitHelper::getQuisionerHasil($user->id);
        }
    }
    public function prosesHasilCanvas(){
        ini_set('max_execution_time', 300);
        $assesment=Assesment::get();
        foreach($assesment as $as){
            CobitHelper::assesmentDfWeight($as->id);
        }
    }

}
