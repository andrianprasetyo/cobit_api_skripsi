<?php

namespace App\Http\Controllers;

use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    use JsonResponse;

    public function run(Request $request)
    {
        Artisan::call($request->command);
        return $this->successResponse();
    }
}
