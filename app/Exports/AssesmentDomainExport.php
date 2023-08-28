<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class AssesmentDomainExport implements FromArray
{

    private $data;

    public function __construct($data)
    {
        $list = [];

        if (!$data->isEmpty()) {
            $no = 1;
            foreach ($data as $_item) {
                $list[] = array(
                    $no,
                    $_item->domain->kode.'-'.strip_tags($_item->domain->ket),
                    $_item->aggreed_capability_level,
                );
                $no++;
            }
        }
        $this->data = $list;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return [
            ['No', 'Governance & Management Objective', 'Target Capability KCI'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
    }
}
