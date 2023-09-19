<?php

namespace App\Http\Requests\Organisasi;

use App\Models\OrganisasiDivisi;
use Illuminate\Foundation\Http\FormRequest;

class JabatanDivisiRequest extends FormRequest
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
            'nama'=>[
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $_chekc_ = OrganisasiDivisi::where('nama', $value)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->exists();

                    if ($_chekc_) {
                        $fail($value . ' Nama divisi/jabatan sudah digunakan');
                    }
                }
            ],
            'jenis'=>'required|in:jabatan,divisi',
            'organisasi_id'=>'required|uuid|exists:organisasi,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'=>'Nama jabatan/divisi harus di isi',
            'nama.string' => 'Nama jabatan/divisi tidak valid',
            'jenis.required' => 'Jenis harus di isi',
            'jenis.in' => 'Jenis tidak valid (jabatan/divisi)',
            'organisasi_id.required' => 'Organisasi harus di isi',
            'organisasi_id.exists' => 'Organisasi tidak terdaftar',
        ];
    }
}
