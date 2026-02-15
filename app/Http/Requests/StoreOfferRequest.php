<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\OfferState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreOfferRequest extends FormRequest
{
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_scalar($this->name ?? null) ? trim((string) $this->name) : null,
            'slug' => is_scalar($this->slug ?? null) ? trim((string) $this->slug) : null,
            'description' => is_scalar($this->description ?? null) ? trim((string) $this->description) : null,
        ]);
    }

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
