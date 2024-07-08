<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserRespondenExport implements FromArray
{
    private $data;

    public function __construct($data)
    {
        $list=[];

        if(!$data->isEmpty())
        {
            $no=1;
            foreach ($data as $_item) {
                $list[]=array(
                    $no,
                    $_item->nama ? $_item->nama : '',
                    $_item->divisi ? $_item->divisi->nama : '',
                    $_item->jabatan ? $_item->jabatan->nama : '',
                    $_item->code ? config('app.url_fe') . '/kuesioner/responden?code=' . $_item->code : '',
                );

                $no++;
            }
        }
        $this->data=$list;
    }

    // public function collection()
    // {
    //     $data = AssessmentUsers::where('assesment_id', $this->data)->get();
    //     return new Collection($data);
    // }

    public function array(): array
    {
        return [
            ['No', 'Nama Lengkap', 'Divisi/Bagian','Jabatan','Link'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
    }

    public function withHyperlinks(Worksheet $sheet)
    {
        foreach ($this->data as $index => $row) {
            if (isset($row[4]) && !empty($row[4])) {
                $cell = 'E' . ($index + 2); // Kolom E dan baris mulai dari 2
                $sheet->getCell($cell)->setValue('Link');
                $sheet->getCell($cell)->getHyperlink()->setUrl($row[4]);
            }
        }
    }

    // public function view(): View
    // {
    //     return view('report.user-responden', [
    //         'data' => $this->data,
    //     ]);
    // }

    /**
    * @return \Illuminate\Support\Collection
    */

    // public function map($row): array
    // {
    //     return [
    //         'Nama Lengkap' => $row['nama'],
    //         'Divisi/Bagian' => $row['divisi'],
    //         'Jabatan' => $row['jabatan'],
    //     ];
    // }

    // public function headings(): array
    // {
    //     return [
    //         'No',
    //         'Nama Lengkap',
    //         'Divisi/Bagian',
    //         'Jabatan'
    //     ];
    // }
}
