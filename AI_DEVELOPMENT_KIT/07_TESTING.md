# 07 Testing Strategy & Automated Assertions

## 1. Test Suite Architecture
- Maintain unit tests in `tests/Unit/` for isolated Service and Repository methods.
- Maintain feature tests in `tests/Feature/` for HTTP endpoints, validation checks, and authorization rules.

---

## 2. Mandatory Test Coverage Rules
Every feature module must include automated feature tests covering:
- **Happy Path**: Successful creation, view, edit, update, and deletion flows.
- **Validation Failure**: Submitting invalid form payloads (asserting 422 HTTP response or session errors).
- **Authorization Boundary**: Ensuring unauthorized users cannot access restricted actions.

```php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Smartphones',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Smartphones']);
    }
}
```

---

## 3. Database & Factory Conventions
- Use `RefreshDatabase` trait for database isolation across test runs.
- Create Model Factories in `database/factories/` for populating clean test state.
- Zero failing tests policy: `php artisan test` must return 100% clean passes.
