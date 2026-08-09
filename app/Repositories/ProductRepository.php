<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

final class ProductRepository implements ProductRepositoryInterface
{
    public function getAdminPaginated(int $perPage = 15, ?string $search = null, bool $showTrashed = false): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images'])
            ->latest();

        if ($showTrashed) {
            $query->onlyTrashed();
        }

        if ($search !== null && trim($search) !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getStorefrontPaginated(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        if (! Schema::hasTable('products')) {
            return new LengthAwarePaginator([], 0, $perPage);
        }

        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($search !== null && trim($search) !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        try {
            return $query->latest()->paginate($perPage);
        } catch (QueryException) {
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'images'])->find($id);
    }

    public function findByIdWithTrashed(int $id): ?Product
    {
        return Product::withTrashed()->with(['category', 'images'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function restore(Product $product): bool
    {
        return (bool) $product->restore();
    }

    public function countLowStock(): int
    {
        return Product::whereRaw('stock <= low_stock_threshold')->count();
    }
}
