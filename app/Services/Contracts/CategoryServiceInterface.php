<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator;

    public function getActiveParentCategories(): Collection;

    public function createCategory(array $data): Category;

    public function updateCategory(Category $category, array $data): bool;

    public function deleteCategory(Category $category): bool;
}
