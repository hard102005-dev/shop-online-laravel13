<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function getAdminProducts(int $perPage = 15, ?string $search = null, bool $showTrashed = false): LengthAwarePaginator
    {
        return $this->productRepository->getAdminPaginated($perPage, $search, $showTrashed);
    }

    public function getStorefrontProducts(?int $categoryId = null, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return $this->productRepository->getStorefrontPaginated($categoryId, $search, $perPage);
    }

    public function createProduct(array $data): Product
    {
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!isset($data['sku']) || empty($data['sku'])) {
            $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $data['is_featured'] = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;
        $data['image_path'] = $this->storeImage($data['image'] ?? null);
        unset($data['image']);

        return $this->productRepository->create($data);
    }

    public function updateProduct(Product $product, array $data): bool
    {
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;
        $data['is_featured'] = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;
        $data['image_path'] = $this->storeImage($data['image'] ?? null, $product->image_path);
        unset($data['image']);

        return $this->productRepository->update($product, $data);
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }

    public function findProductByIdWithTrashed(int $id): ?Product
    {
        return $this->productRepository->findByIdWithTrashed($id);
    }

    public function restoreProduct(Product $product): bool
    {
        return $this->productRepository->restore($product);
    }

    private function storeImage(?UploadedFile $image, ?string $existingPath = null): ?string
    {
        if ($image === null) {
            return $existingPath;
        }

        if ($existingPath !== null && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        return $image->store('products', 'public');
    }
}
