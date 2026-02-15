<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Domain\Enums\Pagination;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ListOffersRequest extends FormRequest
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
        $maxPerPage = 100;

        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.$maxPerPage],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => $this->has('page') ? (int) $this->query('page') : 1,
            'per_page' => $this->has('per_page') ? (int) $this->query('per_page') : Pagination::DefaultPerPage->value,
        ]);
    }
}
