<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService
    ) {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(): View
    {
        $categories = $this->categoryService->getPaginatedCategories(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = $this->categoryService->getActiveParentCategories();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = $this->categoryService->getActiveParentCategories();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->updateCategory($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
