<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getAdminProducts(int $perPage = 15, ?string $search = null, bool $showTrashed = false): LengthAwarePaginator;

    public function getStorefrontProducts(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator;

    public function createProduct(array $data): Product;

    public function updateProduct(Product $product, array $data): bool;

    public function deleteProduct(Product $product): bool;

    public function findProductByIdWithTrashed(int $id): ?Product;

    public function restoreProduct(Product $product): bool;
}
