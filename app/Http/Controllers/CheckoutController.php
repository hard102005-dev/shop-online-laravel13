<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $summary = $this->cartService->getSummary();

        if ($summary['item_count'] === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();

        return view('checkout.index', [
            'summary' => $summary,
            'user' => $user,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $summary = $this->cartService->getSummary();

        if ($summary['item_count'] === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        /** @var User $user */
        $user = $request->user();

        try {
            $order = $this->orderService->createOrder($user, $validated, $summary['items']);
            $this->cartService->clear();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully.');
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
