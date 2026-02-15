<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\ProductState;
use App\Http\Rules\PriceRule;
use App\Http\Rules\SkuRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreProductRequest extends FormRequest
{
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_scalar($this->name ?? null) ? trim((string) $this->name) : null,
            'sku' => is_scalar($this->sku ?? null) ? trim((string) $this->sku) : null,
            'price' => is_numeric($this->price ?? '') ? (float) $this->price : $this->price,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', new SkuRule, 'unique:products,sku'],
            'image' => ['required', 'file', 'image'],
            'price' => ['required', new PriceRule],
            'state' => ['required', Rule::enum(ProductState::class)],
        ];
    }
}
