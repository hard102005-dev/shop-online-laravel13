<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getAdminPaginated(int $perPage = 15, ?string $search = null, bool $showTrashed = false): LengthAwarePaginator;

    public function getStorefrontPaginated(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): ?Product;

    public function findByIdWithTrashed(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): bool;

    public function delete(Product $product): bool;

    public function restore(Product $product): bool;

    public function countLowStock(): int;
}
