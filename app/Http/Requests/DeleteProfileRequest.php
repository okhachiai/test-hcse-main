<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class DeleteProfileRequest extends FormRequest
{
    protected $errorBag = 'userDeletion';

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'password.required' => __('Veuillez confirmer votre mot de passe pour supprimer votre compte.'),
            'password.current_password' => __('Le mot de passe fourni est incorrect.'),
        ];
    }
}
