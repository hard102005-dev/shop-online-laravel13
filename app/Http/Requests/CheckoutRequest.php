<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

final class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $cart = Session::get('cart', []);

            if ($cart === []) {
                $validator->errors()->add('cart', 'Your cart is empty.');

                return;
            }

            foreach ($cart as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(0, (int) ($item['quantity'] ?? 0));

                if ($productId <= 0 || $quantity <= 0) {
                    $validator->errors()->add('cart', 'One or more cart items are invalid.');

                    continue;
                }

                $product = Product::query()->withTrashed()->find($productId);

                if ($product === null) {
                    $validator->errors()->add('cart', 'One or more products are no longer available.');

                    continue;
                }

                if ($product->trashed()) {
                    $validator->errors()->add('cart', 'One or more products are no longer available.');

                    continue;
                }

                if (! $product->is_active) {
                    $validator->errors()->add('cart', 'One or more products are currently unavailable.');

                    continue;
                }

                if ($product->stock < $quantity) {
                    $validator->errors()->add('cart', 'One or more products do not have enough stock to complete your order.');
                }
            }
        });
    }
}
