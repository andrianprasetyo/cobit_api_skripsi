<?php

namespace App\Exports;

use App\Models\QuisionerPertanyaan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RespondenQuisionerHasilExport implements FromArray
{
    private $data;
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return QuisionerHasil::all();
    // }

    public function __construct($data)
    {
        $this->data=$data;
    }

    public function array(): array
    {
        return [
            ['No', 'Nama Lengkap', 'Divisi/Bagian', 'Jabatan'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
    }

    // public function query()
    // {
    //     $pertanyaan = QuisionerPertanyaan::all();
    //     return $pertanyaan;
    // }

    // public function collection()
    // {
    //     return $this->data;
    // }

    // public function array(): array
    // {
    //     return $this->data;
    // }

    // public function headings(): array
    // {
    //     return [
    //         'ID',
    //         'Name',
    //         'Email',
    //         // Add more headings here
    //     ];
    // }

    // public function startCell(): string
    // {
    //     return 'D2';
    // }

    // public function map($row): array
    // {
    //     return [
    //         'Judul' => 'A3',
    //         'Bobot' => 'B4',
    //         // ... add more custom headers and map keys here
    //     ];
    // }

    // public function headings(): array
    // {
    //     return ['Nama', 'Position'];
    //     // return ['A3','B4'];
    // }

    // public function view(): View
    // {
    //     return view('export',['data'=>$this->data]);
    // }

    // public function array(): array
    // {
    //     return [
    //         ['Header 1', 'Header 2', 'Header 3'], // Customize your headers here
    //         // ... (data rows)
    //     ];
    // }
}
