<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService,
        private readonly CategoryServiceInterface $categoryService
    ) {}

    public function index(Request $request): View
    {
        $categoryId = $request->filled('category') ? (int) $request->input('category') : null;
        $search = $request->input('search');

        $products = $this->productService->getStorefrontProducts($categoryId, $search, 12);
        $categories = $this->categoryService->getActiveParentCategories();

        return view('products.index', compact('products', 'categories', 'categoryId', 'search'));
    }

    public function show(string $slug): View
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('products.show', compact('product'));
    }
}
