<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:offers,slug'],
            'image' => ['required', 'image'],
            'description' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'in:draft,published,hidden'],
        ];
    }
}
