<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enums\ProductState;
use App\Http\Rules\PriceRule;
use App\Http\Rules\SkuRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
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
