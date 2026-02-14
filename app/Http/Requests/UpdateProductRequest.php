<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProductState;
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
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product)],
            'image' => ['nullable', 'file', 'image'],
            'price' => ['required', 'numeric', 'min:0'],
            'state' => ['required', Rule::enum(ProductState::class)],
        ];
    }
}
