<?php

namespace App\Exports;

use App\Models\CapabilityTarget;
use App\Models\CapabilityTargetLevel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssesmentDomain2Export implements FromArray, WithColumnWidths, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;

    public function __construct($data, $assesment=null)
    {
        $list = [];


        $target = null;
        if (!$data->isEmpty()) {
            $no = 1;

            foreach ($data as $_item) {
                $target = null;
                $capability_target_default = CapabilityTarget::where('assesment_id', $_item->assesment_id)->where('default', true)->first();
                if ($capability_target_default) {
                    $target_level = CapabilityTargetLevel::where('capability_target_id', $capability_target_default->id)
                        ->where('domain_id', $_item->domain_id)
                        ->first();
                    if ($target_level) {
                        $target = (int) $target_level->target;
                    }
                }
                $list[] = array(
                    $no,
                    $_item->kode . '-' . strip_tags($_item->ket),
                    $_item->suggest_capability_level,
                    $_item->aggreed_capability_level,
                    $target,
                    $_item->aggreed_capability_level >= $assesment->minimum_target ? 'Ya' : 'Tidak',
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
            ['No', 'Governance & Management Objective', 'Target Capability Level', 'Hasil Adjustment','Target BUMN','Assessment'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
    }

    public function columnWidths(): array
    {
        return [
            'B' => 50,
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
