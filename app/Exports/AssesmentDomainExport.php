<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssesmentDomainExport implements FromArray, WithColumnWidths, WithStyles
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
                    $_item->kode.'-'.strip_tags($_item->ket),
                    $_item->aggreed_capability_level,
                    $_item->suggest_capability_level >= $_item->minimum_target?'Ya':'Tidak'
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
            ['No', 'Governance & Management Objective', 'Target Capability Level','Assesment'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
    }

    public function columnWidths(): array
    {
        return [
            'B'=>50,
            'C' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Apply center alignment to cells in range A1:C100
            'C1' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'D1' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
