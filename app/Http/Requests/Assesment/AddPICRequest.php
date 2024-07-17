<?php

namespace App\Http\Requests\Assesment;

use Illuminate\Foundation\Http\FormRequest;

class AddPICRequest extends FormRequest
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
        $validate['users']='required|array';
        // $validate['users.*.email'] = 'email';
        $validate['assesment_id'] = 'required|exists:assesment,id';
        $validate['organisasi_id'] = 'required|exists:organisasi,id';
        return $validate;
    }

    public function messages(): array
    {
        return [
            'users.required'=>'Data user PIC harus di isi',
            'users.array' => 'Data user PIC harus dalam bentuk array/list',

            'assesment_id.required' => 'Assesment ID harus di isi',
            'assesment_id.exists' => 'Assesment ID tidak terdaftar',

            'organisasi_id.required' => 'Organisasi ID harus di isi',
            'organisasi_id.exists' => 'Organisasi ID tidak terdaftar',
        ];
    }
}
