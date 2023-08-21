<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function TemplateInviteResponden()
    {
        $file=public_path('sample/template-invite-respondent.xlsx');
        return response()->download($file);
    }
}
