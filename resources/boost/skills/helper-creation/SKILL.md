---
name: helper-creation
description: Create and generate helper classes using Laravel Dynamic Helpers with proper structure and best practices.
---

# Helper Creation Skill

This skill guides you through creating, organizing, and implementing helper classes for your Laravel application.

## When to Use This Skill

Use this skill when:
- Creating a new helper class for your application
- Organizing helpers into logical groups (nested directories)
- Implementing specific functionality in a helper
- Adding methods to existing helpers
- Setting up helper dependencies via dependency injection

## Primary Commands

### Basic Helper Generation

```bash
php artisan make:helper MoneyHelper
```

Creates two files:
- `app/Helpers/MoneyHelper.php` — Your helper class

### Nested Helper Generation

```bash
php artisan make:helper Store/CreateHelper
php artisan make:helper Admin/User/PermissionHelper
php artisan make:helper Report/Analytics/TrafficHelper
```

Creates the full directory structure automatically.

## Helper Structure

### Basic Helper Template

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class MoneyHelper extends Helper
{
    public function format($amount, $decimals = 2): string
    {
        return number_format($amount, $decimals);
    }

    public function toMinor($amount): int
    {
        return (int) ($amount * 100);
    }
}
```

### Usage

```php
// Automatically registered as moneyHelper()
moneyHelper()->format(1000);        // "1,000.00"
moneyHelper()->toMinor(1500);       // 150000
```

## Naming Conventions

### Helper Class Names
- Use PascalCase with "Helper" suffix
- `MoneyHelper`, `PermissionHelper`, `ReportHelper` ✅
- `Money`, `Permissions`, `Report` ❌

### Function Names (Auto-Generated)
- Simple: `MoneyHelper` → `moneyHelper()`
- Nested: `Store/CreateHelper` → `storeCreateHelper()`
- Deep: `Admin/User/PermissionHelper` → `adminUserPermissionHelper()`

### Method Names
- Use camelCase
- `formatMoney()`, `checkPermission()`, `generateReport()` ✅
- `format_money()`, `check-permission()` ❌

## Creating Helpers with Dependencies

### Using Constructor Injection

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class PermissionHelper extends Helper
{
    public function __construct(
        protected UserRepository $users,
    ) {}

    public function can($permission): bool
    {
        $user = Auth::user();
        return $user && $this->users->hasPermission($user, $permission);
    }

    public function canAny(array $permissions): bool
    {
        return collect($permissions)->some(fn($p) => $this->can($p));
    }
}
```

Dependencies are automatically resolved from the container.

### Injecting Multiple Dependencies

```php
<?php

namespace App\Helpers\Store;

use L0n3ly\LaravelDynamicHelpers\Helper;
use App\Repositories\{ProductRepository, InventoryRepository, OrderRepository};

class CheckoutHelper extends Helper
{
    public function __construct(
        protected ProductRepository $products,
        protected InventoryRepository $inventory,
        protected OrderRepository $orders,
    ) {}

    public function validateCart(array $items): array
    {
        $errors = [];

        foreach ($items as $item) {
            $product = $this->products->find($item['product_id']);
            if (!$product) {
                $errors[] = "Product {$item['product_id']} not found";
                continue;
            }

            $available = $this->inventory->available($product->id);
            if ($available < $item['quantity']) {
                $errors[] = "Only {$available} of {$product->name} available";
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}
```

## Organizing Helpers with Nesting

### Directory Structure

```
app/Helpers/
├── MoneyHelper.php              # Global helpers
├── PermissionHelper.php
├── Store/                       # Store-specific
│   ├── CreateHelper.php
│   ├── UpdateHelper.php
│   └── CheckoutHelper.php
├── Admin/                       # Admin-specific
│   ├── DashboardHelper.php
│   └── User/
│       ├── PermissionHelper.php
│       └── RoleHelper.php
└── Report/                      # Report-specific
    ├── GeneratorHelper.php
    └── Analytics/
        ├── TrafficHelper.php
        └── ConversionHelper.php
```

### Usage

```php
// Global
moneyHelper()->format(100);

// Store-specific
storeCreateHelper()->create($data);
storeCheckoutHelper()->validateCart($items);

// Admin-specific
adminDashboardHelper()->getDashboardData();
adminUserPermissionHelper()->assign($user, $permission);

// Reports
reportGeneratorHelper()->generate();
reportAnalyticsTrafficHelper()->getMetrics();
```

## Common Helper Patterns

### Single Responsibility

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class CurrencyHelper extends Helper
{
    public function convert($amount, $from, $to): float
    {
        // Handle currency conversion
    }

    public function format($amount, $currency = 'USD'): string
    {
        // Format amount with symbol
    }

    public function getSymbol($currency): string
    {
        // Get currency symbol
    }
}
```

### API Response Formatting

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class ApiResponseHelper extends Helper
{
    public function success($data, $message = 'Success', $code = 200): array
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ];
    }

    public function error($message, $code = 400, $data = null): array
    {
        return [
            'success' => false,
            'error' => $message,
            'code' => $code,
            'data' => $data,
        ];
    }

    public function paginated($data, $total, $perPage = 15): array
    {
        return $this->success([
            'items' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'pages' => ceil($total / $perPage),
        ]);
    }
}
```

### Data Transformation

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class UserTransformerHelper extends Helper
{
    public function toArray($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'active' => $user->active,
            'created_at' => $user->created_at->toIso8601String(),
        ];
    }

    public function toJson($user): string
    {
        return json_encode($this->toArray($user));
    }

    public function toCollection($users)
    {
        return collect($users)->map(fn($u) => $this->toArray($u));
    }
}
```

### Business Logic

```php
<?php

namespace App\Helpers\Store;

use L0n3ly\LaravelDynamicHelpers\Helper;
use App\Models\Order;

class OrderHelper extends Helper
{
    public function calculateDiscount($subtotal, $discountPercent): float
    {
        return ($subtotal * $discountPercent) / 100;
    }

    public function calculateTax($subtotal, $taxRate): float
    {
        return ($subtotal * $taxRate) / 100;
    }

    public function calculateTotal($subtotal, $taxRate, $discountPercent = 0): float
    {
        $discount = $this->calculateDiscount($subtotal, $discountPercent);
        $taxable = $subtotal - $discount;
        $tax = $this->calculateTax($taxable, $taxRate);
        return $taxable + $tax;
    }

    public function isEligibleForFreeShipping($total): bool
    {
        return $total >= config('store.free_shipping_threshold', 100);
    }
}
```

## Adding Methods to Existing Helpers

Simply edit the helper file and add new public methods:

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class MoneyHelper extends Helper
{
    public function format($amount): string { /* ... */ }
    public function toMinor($amount): int { /* ... */ }

    // Add new method - instantly available!
    public function distribute($amount, $parts): array
    {
        $perPart = $amount / $parts;
        return array_fill(0, $parts, $perPart);
    }
}
```

Then use immediately:
```php
moneyHelper()->distribute(1000, 4);  // [250, 250, 250, 250]
```

## Testing Helpers

### Unit Test Example

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;

class MoneyHelperTest extends TestCase
{
    public function test_format_rounds_to_two_decimals()
    {
        $result = moneyHelper()->format(1000.556);
        $this->assertEquals('1,000.56', $result);
    }

    public function test_format_handles_negative_amounts()
    {
        $result = moneyHelper()->format(-500);
        $this->assertEquals('-500.00', $result);
    }

    public function test_to_minor_converts_to_cents()
    {
        $result = moneyHelper()->toMinor(10.50);
        $this->assertEquals(1050, $result);
    }
}
```

### Integration Test Example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class PermissionHelperTest extends TestCase
{
    public function test_user_can_access_permitted_action()
    {
        $user = User::factory()->create();
        $user->givePermission('edit-posts');

        $this->assertTrue(permissionHelper()->can('edit-posts'));
    }

    public function test_user_cannot_access_denied_action()
    {
        $user = User::factory()->create();
        $this->assertFalse(permissionHelper()->can('delete-users'));
    }
}
```

## Best Practices

1. **Keep Helpers Focused**
   - One responsibility per helper
   - Don't mix concerns (data transformation + business logic)

2. **Use Type Hints**
   ```php
   public function format(float $amount, int $decimals = 2): string
   ```

3. **Add Documentation**
   ```php
   /**
    * Format amount as currency
    *
    * @param float $amount
    * @param int $decimals
    * @return string Formatted amount (e.g., "1,000.00")
    */
   public function format(float $amount, int $decimals = 2): string
   ```

4. **Organize by Feature**
   - Group related helpers in subdirectories
   - Use nested structure for clarity

5. **Inject Dependencies**
   - Use constructor injection for repositories
   - Avoid using `app()` inside helpers

## Examples

### Complete Helper Example

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class SecurityHelper extends Helper
{
    public function __construct(
        protected UserRepository $users,
    ) {}

    public function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return Auth::user()?->role === 'super-admin';
    }

    public function can($permission): bool
    {
        $user = Auth::user();
        return $user && $this->users->hasPermission($user->id, $permission);
    }

    public function canAny(array $permissions): bool
    {
        return collect($permissions)->some(fn($p) => $this->can($p));
    }

    public function canAll(array $permissions): bool
    {
        return collect($permissions)->every(fn($p) => $this->can($p));
    }
}
```

### Usage in Controller

```php
<?php

namespace App\Http\Controllers;

class PostController extends Controller
{
    public function edit($id)
    {
        $post = Post::find($id);

        if (!securityHelper()->can('edit-posts')) {
            abort(403);
        }

        return view('posts.edit', ['post' => $post]);
    }
}
```

### Usage in Middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdminAccess
{
    public function handle($request, Closure $next)
    {
        if (!securityHelper()->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
```

---

**Next Steps:**
- See "Function Registration" skill to understand how functions work
- See "Nested Helpers" skill for organizing complex helper structures
