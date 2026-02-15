<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\OfferState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'state' => ['required', Rule::enum(OfferState::class)],
        ];
    }
}
