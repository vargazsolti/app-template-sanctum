<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }
}
