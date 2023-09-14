<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SummaryCapabilityAssesmentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('report.summary-capability-assesment', [
            // 'header' => $this->header,
            'data' => $this->data
        ]);
    }
}
