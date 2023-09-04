<?php

namespace App\Http\Requests\Quisioner;

use App\Models\Assesment;
use App\Models\Quisioner;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class QuisionerStartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'=> 'required|uuid|exists:assesment_users,id',
            'assesment_id' => [
                'required',
                'uuid',
                'exists:assesment,id',
                function($attr,$value,$fail){
                    $_assesment=Assesment::find($value);
                    $currentDate = Carbon::now();
                    $start_date = Carbon::parse($_assesment->assesment->start_date)->format('Y-m-d');
                    $end_date=Carbon::parse($_assesment->assesment->end_date)->format('Y-m-d');

                    if (!$currentDate->startOfDay()->gte($start_date)) {
                        $fail('Assesment quisoner dimulai pada ' . $start_date, 400);
                    }

                    if (Carbon::now()->gte($end_date)) {
                        $fail('Assesment quisoner telah selesai pada ' . $end_date, 400);
                    }
                }
            ],
            'nama'=>'required',
            // 'quisioner_id' => [
            //     'required',
            //     'uuid',
            //     'exists:quisioner,id',
            //     function ($attr, $value, $fail) {
            //         $quisioner = Quisioner::find($value);
            //         if (!$quisioner->aktif) {
            //             $fail('Quisioner (' . $quisioner->title . ') tidak aktif');
            //         }
            //     }
            // ],
        ];
    }

    public function messages(): array
    {
        $validate['id.required'] = 'Responden ID harus di isi';
        $validate['id.uuid'] = 'Responden ID tidak valid';
        $validate['id.exists'] = 'Responden ID tidak terdaftar';

        $validate['assesment_id.required'] = 'Assesment ID harus di isi';
        $validate['assesment_id.uuid'] = 'Assesment ID tidak valid';
        $validate['assesment_id.exists'] = 'Assesment ID tidak terdaftar';

        // $validate['quisioner_id.required'] = 'Quisioner ID harus di isi';
        // $validate['quisioner_id.uuid'] = 'Quisioner ID tidak valid';
        // $validate['quisioner_id.exists'] = 'Quisioner ID tidak terdaftar';

        $validate['nama.required'] = 'Nama responden harus di isi';
        return $validate;
    }
}
