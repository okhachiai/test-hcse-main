<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\ProductState;
use App\Http\Rules\PriceRule;
use App\Http\Rules\SkuRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', new SkuRule, Rule::unique('products', 'sku')->ignore($product)],
            'image' => ['nullable', 'file', 'image'],
            'price' => ['required', new PriceRule],
            'state' => ['required', Rule::enum(ProductState::class)],
        ];
    }
}
