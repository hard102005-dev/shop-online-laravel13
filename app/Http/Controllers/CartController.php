<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(): View
    {
        return view('cart.index', $this->cartService->getSummary());
    }

    public function add(StoreCartItemRequest $request, string $product): RedirectResponse
    {
        $productModel = Product::query()->withTrashed()->find((int) $product);

        if ($productModel === null) {
            return back()->with('error', 'The selected product could not be found.');
        }

        try {
            $this->cartService->addItem($productModel, max(1, (int) $request->input('quantity', 1)));

            return redirect()->route('cart.index')
                ->with('success', 'Product added to cart.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function update(UpdateCartItemRequest $request, int $productId): RedirectResponse
    {
        try {
            $this->cartService->updateItem($productId, (int) $request->input('quantity', 1));

            return redirect()->route('cart.index')->with('success', 'Cart updated.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function remove(int $productId): RedirectResponse
    {
        $this->cartService->removeItem($productId);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
