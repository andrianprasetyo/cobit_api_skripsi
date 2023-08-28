<?php

namespace App\Http\Requests\Assesment;

use App\Models\AssessmentUsers;
use Illuminate\Foundation\Http\FormRequest;

class inviteRespondenRequest extends FormRequest
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
        // $validate['id'] = 'required|exists:assesment,id';
        // $validate['email'] = [
        //     'required',
        //     'array',
        //     function ($attribute, $value, $fail) use () {
        //         $_chekc_exists_mail = AssessmentUsers::select('email')
        //             ->where('assesment_id', $request->id)
        //             ->whereIn('email', $value)
        //             ->get();

        //         if (!$_chekc_exists_mail->isEmpty()) {
        //             $mail = [];
        //             foreach ($_chekc_exists_mail as $_item_mail) {
        //                 $mail[] = $_item_mail->email;
        //             }
        //             $fail('Terdapat email yang sudah terdaftar pada assesment yang sama (' . implode(',', $mail) . ')');
        //         }
        //     }
        // ];
        // $validate['email.*'] = 'required|email';
        // return $validate;
        return [];
    }
}
