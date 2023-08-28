<?php

namespace App\Http\Requests\Canvas;

use Illuminate\Foundation\Http\FormRequest;

class AdjustmenValueCanvasRequest extends FormRequest
{
    /**
     * Determine if the canvas is authorized to make this request.
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
            'id' => ['required', 'uuid', 'exists:assesment_canvas,id'],
            'nilai'=>['required','numeric','min:-100','max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Assesment canvas ID harus di isi',
            'id.uuid' => 'Assesment canvas ID tidak valid',
            'id.exists' => 'Assesment canvas ID tidak terdaftar',
            'nilai.required' => 'Nilai adjustmen harus di isi',
            'nilai.numeric' => 'Nilai adjustmen tidak valid',
            'nilai.min' => 'Nilai adjustmen min -100',
            'nilai.max' => 'Nilai adjustmen max 100',
        ];
    }
}
