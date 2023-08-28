<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

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
                    $_item->nama,
                    $_item->divisi,
                    $_item->jabatan,
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
            ['No', 'Nama Lengkap', 'Divisi/Bagian','Jabatan'], // Customize your headers here
            $this->data
        ];
        // return $this->data;
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
