<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $productId = $this->route('productId');

        if ($productId !== null) {
            $this->merge(['product_id' => (int) $productId]);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product could not be found.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $product = Product::query()->withTrashed()->find($this->input('product_id'));

            if ($product === null) {
                return;
            }

            if ($product->trashed()) {
                $validator->errors()->add('product_id', 'This product is no longer available.');

                return;
            }

            if (! $product->is_active) {
                $validator->errors()->add('product_id', 'This product is currently unavailable.');

                return;
            }

            $quantity = max(1, (int) $this->input('quantity', 1));

            if ($quantity > $product->stock) {
                $validator->errors()->add('quantity', 'The requested quantity exceeds the available stock.');
            }
        });
    }
}
