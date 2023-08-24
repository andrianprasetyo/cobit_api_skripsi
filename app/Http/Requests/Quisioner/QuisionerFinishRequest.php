<?php

namespace App\Http\Requests\Quisioner;

use Illuminate\Foundation\Http\FormRequest;

class QuisionerFinishRequest extends FormRequest
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
            'assesment_user_id' => ['required', 'uuid', 'exists:assesment_users,id'],
            'assesment_user_id.required' => 'Assesment user ID harus di isi',
            'assesment_user_id.uuid' => 'Assesment user ID tidak valid',
            'assesment_user_id.exists' => 'Assesment user ID tidak terdaftar',
        ];
    }

    public function messages(): array
    {
        return [
            'assesment_user_id.required' => 'Assesment user ID harus di isi',
            'assesment_user_id.uuid' => 'Assesment user ID tidak valid',
            'assesment_user_id.exists' => 'Assesment user ID tidak terdaftar',
        ];
    }
}
