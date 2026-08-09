# 02 Layered Architecture: Service & Repository Pattern

## Layered System Architecture

This application strictly follows a **4-Tier Architecture** to ensure separation of concerns, high testability, and clean code maintainability:

```
[ HTTP Request / Route ]
           |
           v
   [ Controller Layer ]  <--- Form Request Validation & Response Rendering
           |
           v
    [ Service Layer ]    <--- Business Logic, Tax/Cart Calculations, Workflows
           |
           v
  [ Repository Layer ]   <--- Database Queries, Filtering, Scope Wrappers
           |
           v
    [ Eloquent Model ]   <--- Database Schemas, Relationships, Casts
```

---

## 1. Controller Layer (`App\Http\Controllers\`)
- Responsible **only** for receiving requests, invoking the Service layer, and returning Blade views or JSON responses.
- Must **never** execute raw database queries or complex inline business calculations.

```php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\RedirectResponse;

final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {}

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct($request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }
}
```

---

## 2. Service Layer (`App\Services\`)
- Enapsulates domain logic, financial calculations, third-party API invocations, and multi-step transaction workflows.
- Implements interface contracts (`ProductServiceInterface`).

---

## 3. Repository Layer (`App\Repositories\`)
- Abstracts all database queries. Interfaces (`ProductRepositoryInterface`) defined under `App\Repositories\Contracts\`.
- Encapsulates eager loading (`with()`), pagination, and custom filtering.

```php
namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductRepository implements ProductRepositoryInterface
{
    public function getActivePaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }
}
```
