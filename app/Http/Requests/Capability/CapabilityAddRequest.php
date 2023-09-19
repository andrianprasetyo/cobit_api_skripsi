<?php

namespace App\Http\Requests\Capability;

use Illuminate\Foundation\Http\FormRequest;

class CapabilityAddRequest extends FormRequest
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
            'kode'=>[
                'required',
                // 'unique:capability_level,kode'
            ],
            'level' => 'required',
            'bobot' => 'required',
            'translate' => 'required',
            'domain_id' => [
                'required',
                'uuid',
                'exists:domain,id'
            ],
            'kegiatan' => [
                'required',
                // 'unique:level_kemampuan,kegiatan'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'kode harus di isi',
            // 'kode.unique' => 'Kode sudah digunakan',
            'domain_id.required' => 'Domain harus di isi',
            'domain_id.exists' => 'Domain tidak terdaftar',
            'level.required' => 'Level harus di isi',
            'bobot.required' => 'Bobot harus di isi',
            'kegiatan.required' => 'Kebiatan harus di isi',
            'translate.required' => 'Translate harus di isi',
        ];
    }
}
