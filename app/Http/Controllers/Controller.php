<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $account;
    protected $assesment=null;

    public function __construct()
    {
        if (Auth::check())
        {
            $this->account = auth()->user();
            $this->assesment = auth()->user()->assesment;
        }
    }
}
