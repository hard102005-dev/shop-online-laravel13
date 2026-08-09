<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllPaginated($perPage);
    }

    public function getActiveParentCategories(): Collection
    {
        return $this->categoryRepository->getActiveParents();
    }

    private function storeImage(?UploadedFile $image, ?string $existingPath = null): ?string
    {
        if ($image === null) {
            return $existingPath;
        }

        if ($existingPath !== null && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        return $image->store('categories', 'public');
    }

    public function createCategory(array $data): Category
    {
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $data['image_path'] = $this->storeImage($data['image'] ?? null);
        unset($data['image']);

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;
        $data['image_path'] = $this->storeImage($data['image'] ?? null, $category->image_path);
        unset($data['image']);

        return $this->categoryRepository->update($category, $data);
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }
}
