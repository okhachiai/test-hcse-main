<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\OfferState;
use App\Http\Rules\OfferCanBePublishedRule;
use App\Models\Offer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateOfferRequest extends FormRequest
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
        /** @var Offer $offer */
        $offer = $this->route('offer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('offers', 'slug')->ignore($offer)],
            'image' => ['nullable', 'file', 'image'],
            'description' => ['nullable', 'string', 'max:255'],
            'state' => [
                'required',
                Rule::enum(OfferState::class),
                new OfferCanBePublishedRule($offer),
            ],
        ];
    }
}
