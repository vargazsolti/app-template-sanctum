<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $user = $this->route('user'); // ApiUser route binding

        return [
            'name' => ['sometimes','string','max:100'],
            'email' => [
                'sometimes','email','max:255',
                Rule::unique('users','email')->ignore($user?->id)
            ],
            // Ha jelszót küldünk, legyen minimum 8 és megerősítve (password_confirmation)
            'password' => ['sometimes','string','min:8','confirmed'],
        ];
    }
}
