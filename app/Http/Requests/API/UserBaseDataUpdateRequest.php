<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserBaseDataUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Route param NÉV: user_basedatum  (ld. routes/api.php)
        $basedatum = $this->route('user_basedatum');

        return [
            'user_id' => [
                'sometimes','integer','exists:users,id',
                Rule::unique('user_basedata','user_id')->ignore($basedatum?->id)
            ],
            'full_name' => ['sometimes','string','max:255'],
            'mothers_name' => ['sometimes','string','max:255'],
            'birth_date' => ['sometimes','date'],
            'birth_place' => ['sometimes','string','max:255'],
            'id_card_number' => [
                'sometimes','string','max:255',
                Rule::unique('user_basedata','id_card_number')->ignore($basedatum?->id)
            ],
            'social_security_number' => [
                'sometimes','string','max:255',
                Rule::unique('user_basedata','social_security_number')->ignore($basedatum?->id)
            ],
        ];
    }
}
