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
            'assement_id' => ['required', 'uuid', 'exists:assesment,id'],
            // 'nilai'=>['required','numeric','min:-100','max:100'],
            'data'=>'required',
            'data.hasil' => 'required|array',
            // 'data.hasil.*.assesmentcanvas' => 'required',
            'data.hasil.*.assesmentcanvas.id' => 'required|exists:assesment_canvas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assement_id.required' => 'Assesment ID harus di isi',
            'assement_id.exists' => 'Assesment ID tidak terdaftar',
            // 'id.uuid' => 'Assesment canvas ID tidak valid',
            // 'id.exists' => 'Assesment canvas ID tidak terdaftar',
            // 'nilai.required' => 'Nilai adjustmen harus di isi',
            // 'nilai.numeric' => 'Nilai adjustmen tidak valid',
            // 'nilai.min' => 'Nilai adjustmen min -100',
            // 'nilai.max' => 'Nilai adjustmen max 100',

            'data.required' => 'Key Data harus di isi',
            'data.hasil.array' => 'Key Data harus dalam bentuk array|list',

            // 'data.*.hasil.required' => 'Key Hasil harus di isi',
            // 'data.*.hasil.array' => 'Key Hasil harus dalam bentuk array|list',

            // 'data.hasil.*.assesmentcanvas.required' => 'Key assesmentcanvas harus di isi',
            'data.hasil.*.assesmentcanvas.id.required' => 'Key assesmentcanvas harus di isi',
            'data.hasil.*.assesmentcanvas.id.exists' => 'ID assesmentcanvas tidak terdaftar',
        ];
    }
}
