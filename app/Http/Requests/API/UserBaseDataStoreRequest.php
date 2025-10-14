<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserBaseDataStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => ['required','integer','exists:users,id','unique:user_basedata,user_id'],
            'full_name' => ['required','string','max:255'],
            'mothers_name' => ['required','string','max:255'],
            'birth_date' => ['required','date'],
            'birth_place' => ['required','string','max:255'],
            'id_card_number' => ['required','string','max:255','unique:user_basedata,id_card_number'],
            'social_security_number' => ['required','string','max:255','unique:user_basedata,social_security_number'],
        ];
    }
}
