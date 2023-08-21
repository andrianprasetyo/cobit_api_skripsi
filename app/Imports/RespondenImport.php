<?php

namespace App\Imports;

use App\Models\Assesment;
use App\Models\Responden;
use App\Notifications\InviteRespondenNotif;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Notification;

class RespondenImport implements ToModel,WithValidation, WithHeadingRow, WithStartRow
{

    private $assesment_id;

    public function __construct($assesment_id)
    {
        $this->assesment_id=$assesment_id;
    }

    public function rules():array
    {
        return [
            'nama' => 'required',
            'email' => 'required|email|unique:responden,email',
        ];
    }

    public function customValidationMessages():array
    {
        return [
            'nama.required' => 'Nama harus di isi (Baris :attribute)',
            'email.required' => 'Email harus di isi (Baris :attribute)',
            'email.email' => 'Email tidak valid (Baris :attribute)',
            'email.unique' => 'Email sudah digunakan (Baris :attribute)',
            // Add more custom messages for other validation rules and columns
        ];
    }

    public function startRow(): int
    {
        return 2; // Skip the first two rows (header and an extra row)
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        // return new Responden([
        //     'nama'=>$row['nama'],
        //     'email' => $row['email'],
        //     'divisi' => $row['divisi'],
        //     'posisi' => $row['posisi'],
        //     'assesment_id'=>$this->assesment_id,
        //     'status'=>'active'
        // ]);

        $responden = new Responden();
        $responden->email = $row['email'];
        if(isset($row['nama']) && $row['nama'] !='')
        {
            $responden->nama = $row['nama'];
        }
        if (isset($row['divisi']) && $row['divisi'] != '') {
            $responden->divisi = $row['divisi'];
        }
        if (isset($row['posisi']) && $row['posisi'] != '') {
            $responden->posisi = $row['posisi'];
        }
        $responden->assesment_id = $this->assesment_id;
        $responden->status = 'active';
        $responden->save();

        $assesment = Assesment::with('organisasi')->find($this->assesment_id);
        $organisasi = $assesment->organisasi;
        Notification::send($responden, new InviteRespondenNotif($organisasi));
    }
}
