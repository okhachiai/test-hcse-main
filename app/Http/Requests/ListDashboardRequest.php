<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\OfferState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class ListDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validStates = array_map(fn (OfferState $s) => $s->value, OfferState::cases());

        return [
            'state' => ['nullable', 'string', Rule::in($validStates)],
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'state' => $this->query('state') ? trim((string) $this->query('state')) : null,
            'name' => $this->query('name') ? trim((string) $this->query('name')) : null,
            'slug' => $this->query('slug') ? trim((string) $this->query('slug')) : null,
        ]);
    }
}
