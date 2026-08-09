<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getActiveParents(): Collection;

    public function findById(int $id): ?Category;

    public function findBySlug(string $slug): ?Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): bool;

    public function delete(Category $category): bool;
}
