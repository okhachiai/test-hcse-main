<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $offerId = (int) $this->route('offerId');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('offers', 'slug')->ignore($offerId)],
            'image' => ['nullable', 'file', 'image'],
            'description' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'in:draft,published,hidden'],
        ];
    }
}
