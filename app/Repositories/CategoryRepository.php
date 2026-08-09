<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

final class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Category::with(['parent', 'children'])
            ->orderBy('sort_order', 'asc')
            ->paginate($perPage);
    }

    public function getActiveParents(): Collection
    {
        if (! Schema::hasTable('categories')) {
            return new Collection();
        }

        return Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::with(['parent', 'children'])->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::with(['parent', 'children', 'products' => function ($q) {
            $q->where('is_active', true)->with('images');
        }])->where('slug', $slug)->first();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->forceDelete();
    }
}
