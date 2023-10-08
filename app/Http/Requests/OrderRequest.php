<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Rules\CanMakeOrderRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products' => ['required', 'array', 'min:1', new CanMakeOrderRule()],
            'products.*.product_id' => ['required', Rule::exists(Product::class, 'id')],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

}
