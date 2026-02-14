<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\OfferState;
use App\Http\Rules\OfferCanBePublishedRule;
use App\Models\Offer;
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
