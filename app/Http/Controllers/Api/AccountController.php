<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use JsonResponse;
    public function me()
    {
        $data=auth()->payload();
        return $this->successResponse($data);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse();
    }
}
