<?php

namespace App\Http\Requests\Canvas;

use Illuminate\Foundation\Http\FormRequest;

class AdjustmenWeightValueRequest extends FormRequest
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
            'assesment_id' => ['required', 'uuid', 'exists:assesment,id'],
            'design_faktor_id' => ['required', 'uuid', 'exists:design_faktor,id'],
            'nilai' => [
                'required',
                'numeric',
                //'min:0',
                // 'max:100'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assesment_id.required' => 'Assesment ID harus di isi',
            'assesment_id.uuid' => 'Assesment ID tidak valid',
            'assesment_id.exists' => 'Assesment ID tidak terdaftar',

            'design_faktor_id.required' => 'Design Faktor ID harus di isi',
            'design_faktor_id.uuid' => 'Design Faktor ID tidak valid',
            'design_faktor_id.exists' => 'Design Faktor ID tidak terdaftar',

            'nilai.required' => 'Nilai weight harus di isi',
            'nilai.numeric' => 'Nilai weight tidak valid',
            // 'nilai.min' => 'Nilai weight min 0',
            // 'nilai.max' => 'Nilai adjustmen max 100',
        ];
    }
}
