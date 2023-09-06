<?php

namespace App\Http\Requests\Capability;

use Illuminate\Foundation\Http\FormRequest;

class CapabilityAnswerAddRequest extends FormRequest
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
            'jawaban'=>[
                'required',
                'array',
            ],
            'jawaban.*.nama'=>[
                'required',
                'string',
                // 'unique:capability_answer,nama'
            ],
            'jawaban.*.bobot'=>[
                'required',
                'numeric',
                // 'between:0,1.00'
                // 'decimal:2'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'jawaban.required'=>'Jawanan harus di isi',
            'jawaban.array' => 'Jawanan harus dalam bentuk array',
            'jawaban.*.nama.required' => 'Nama harus di isi',
            // 'jawaban.*.nama.unique' => 'Nama sudah digunakan',
            'jawaban.*.bobot.required' => 'Nilai harus di isi',
            'jawaban.*.bobot.numeric' => 'Nilai bobot harus dalam bentuk angka/desimal 0 s/d 1',
            // 'jawaban.*.bobot.between' => 'Nilai bobot harus dalam bentuk angka/desimal 0 s/d 1',
        ];
    }
}
