<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AnalisaGapExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data, $header;
    public function __construct($data, $header=null)
    {
        $list = [];
        $this->data = $data;
        $this->header = $header;
    }
    public function view(): View
    {
        return view('report.analisa-gap', [
            // 'header' => $this->header,
            'hasil' => $this->data
        ]);
    }
}
