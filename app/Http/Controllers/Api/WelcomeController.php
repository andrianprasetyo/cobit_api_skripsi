<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\JsonResponse;

class WelcomeController extends Controller
{
    use JsonResponse;
    
    public function halo()
    {
        return $this->successResponse();
    }
}
