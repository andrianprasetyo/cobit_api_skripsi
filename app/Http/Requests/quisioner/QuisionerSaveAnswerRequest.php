<?php

namespace App\Http\Requests\Quisioner;

use Illuminate\Foundation\Http\FormRequest;

class QuisionerSaveAnswerRequest extends FormRequest
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
            'quisioner_id'=> 'required|uuid|exists:quisioner,id',
            'quisioner_pertanyaan_id'=> 'required|uuid|exists:quisioner_pertanyaan,id',
            'jenis_grup'=>'required|in:pilgan,persentase',
            'quisioner_jawaban_id'=>'required|uuid|exists:quisioner_jawaban,id',
            'assesment_user_id'=>'required|uuid|exists:assesment_users,id',
            'design_faktor_komponen_id'=> 'required|uuid|exists:design_faktor_komponen,id',
            'bobot'=> 'required',
        ];
    }

    public function messages(): array
    {
        $validate['quisioner_id.required'] = 'Quisioner ID harus di isi';
        $validate['quisioner_id.uuid'] = 'Quisioner ID tidak valid';
        $validate['quisioner_id.exists'] = 'Quisioner ID tidak terdaftar';
        $validate['quisioner_pertanyaan_id.required'] = 'Quisioner Pertanyaan ID harus di isi';
        $validate['quisioner_pertanyaan_id.uuid'] = 'Quisioner Pertanyaan ID tidak valid';
        $validate['quisioner_pertanyaan_id.exists'] = 'Quisioner Pertanyaan ID tidak terdaftar';
        $validate['jenis_grup.required'] = 'Jenis grup jawaban harus di isi';
        $validate['jenis_grup.in'] = 'Jenis grup tidak valid (pilgan,persentase)';
        $validate['quisioner_jawaban_id.required'] = 'Quisioner jawaban ID harus di isi';
        $validate['quisioner_jawaban_id.uuid'] = 'Quisioner jawaban ID tidak valid';
        $validate['quisioner_jawaban_id.exists'] = 'Quisioner jawaban ID tidak terdaftar';
        $validate['assesment_user_id.required'] = 'Asessment user ID harus di isi';
        $validate['assesment_user_id.uuid'] = 'Asessment user ID tidak valid';
        $validate['assesment_user_id.exists'] = 'Asessment user ID tidak terdaftar';
        $validate['design_faktor_komponen_id.required'] = 'Design faktor ID harus di isi';
        $validate['design_faktor_komponen_id.uuid'] = 'Design faktor ID tidak valid';
        $validate['design_faktor_komponen_id.exists'] = 'Design faktor ID tidak terdaftar';
        $validate['bobot.required'] = 'Bobot harus di isi';
        // $validate['bobot.number'] = 'Bobot harus dalam bentuk angka';

        return $validate;
    }
}
