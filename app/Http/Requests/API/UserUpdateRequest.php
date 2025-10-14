<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Route model binding: {user} -> App\Models\API\ApiUser $user
        $user = $this->route('user');

        return [
            'name' => ['sometimes','string','max:100'],
            'email' => [
                'sometimes','email','max:255',
                Rule::unique('users','email')->ignore($user?->id)
            ],
            // jelszó opcionális; ha küldöd, a controller hash-eli
            'password' => ['sometimes','string','min:6'],
        ];
    }
}
