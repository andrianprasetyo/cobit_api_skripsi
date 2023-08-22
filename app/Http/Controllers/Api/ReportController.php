<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use JsonResponse;

    public function tes(Request $request)
    {
        CobitHelper::getQuisionerHasil('99f2eafc-d602-4815-96c4-74774e3e41dc');
        return $this->successResponse();
    }
}
