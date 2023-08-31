<?php
namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class QuesionerResultExport implements FromView
{
    private $data, $header;
    public function __construct($data,$header)
    {
        $list = [];
        $this->data=$data;
        $this->header=$header;
    }
    public function view(): View
    {
        return view('report.quesionerresult', [
            'header'=>$this->header,
            'hasil'=>$this->data
        ]);
    }
}