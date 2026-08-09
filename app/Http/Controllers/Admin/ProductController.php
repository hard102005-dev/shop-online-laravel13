<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService,
        private readonly CategoryServiceInterface $categoryService
    ) {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $showTrashed = $request->boolean('trashed');
        $products = $this->productService->getAdminProducts(15, $search, $showTrashed);

        return view('admin.products.index', compact('products', 'search', 'showTrashed'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getActiveParentCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct($request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = $this->categoryService->getActiveParentCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct($product, $request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function restore(int $productId): RedirectResponse
    {
        $product = $this->productService->findProductByIdWithTrashed($productId);

        abort_if($product === null, 404);

        $this->authorize('restore', $product);
        $this->productService->restoreProduct($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product restored successfully.');
    }
}
