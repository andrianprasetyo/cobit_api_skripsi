<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RespondenQuisionerHasilExport implements FromView
{
    private $data;
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return QuisionerHasil::all();
    // }

    public function __construct(array $data)
    {
        $this->data=$data;
    }

    // public function array(): array
    // {
    //     return $this->data;
    // }

    // public function map($row): array
    // {
    //     return [
    //         'Judul' => 'A3',
    //         'Bobot' => 'B4',
    //         // ... add more custom headers and map keys here
    //     ];
    // }

    // public function startCell(): string
    // {
    //     return 'A3';
    // }

    // public function headings(): array
    // {
    //     return ['Nama', 'Position'];
    //     // return ['A3','B4'];
    // }

    public function view(): View
    {
        return view('report.quisionerhasil',$this->data);
    }

    // public function array(): array
    // {
    //     return [
    //         ['Header 1', 'Header 2', 'Header 3'], // Customize your headers here
    //         // ... (data rows)
    //     ];
    // }
}
