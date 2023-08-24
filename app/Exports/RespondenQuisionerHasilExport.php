<?php

namespace App\Exports;

use App\Models\AssessmentUsers;
use Maatwebsite\Excel\Concerns\FromCollection;

class RespondenQuisionerHasilExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssessmentUsers::all();
    }
}
