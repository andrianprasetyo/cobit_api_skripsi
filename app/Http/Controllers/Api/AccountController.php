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
        $data=auth()->user();
        return $this->successResponse($data);
    }

    public function refresh()
    {
        $data = [
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];

        return $this->successResponse($data);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse();
    }
}
