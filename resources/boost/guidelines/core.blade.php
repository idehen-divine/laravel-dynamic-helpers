# Laravel Dynamic Helpers

## Helper Classes

- All helpers live in `app/Helpers/` directory and extend `L0n3ly\LaravelDynamicHelpers\Helper`
- Helper class names use PascalCase with "Helper" suffix: `MoneyHelper`, `PermissionHelper`
- One responsibility per helper — don't mix concerns

## Function Registration

- Functions are automatically registered at service provider boot time with proper type hints
- File paths convert to function names: `Store/CreateHelper.php` → `storeCreateHelper()` function
- All registered functions have return type hints: `function moneyHelper(): \App\Helpers\MoneyHelper`
- Use direct function calls: `moneyHelper()->format()` instead of `helpers()->moneyHelper()->format()`

## Creating Helpers

```bash
php artisan make:helper MoneyHelper
php artisan make:helper Store/CreateHelper
```

No additional commands needed — functions auto-register immediately.

## Public Methods

- All public methods become callable via the function: `public function format()` → `moneyHelper()->format()`
- Private/protected methods are internal use only
- Add type hints to all method parameters and return types

## Dependency Injection

- Use constructor property promotion for dependencies: `public function __construct(protected CurrencyRepository $currencies) {}`
- Container automatically resolves injected dependencies
- Helpers are singletons — instantiated once per request

## Organizing Helpers

- Use subdirectories to organize related helpers: `Admin/`, `Store/`, `Report/`
- Keep directory structure shallow (2-3 levels max)
- Group by feature/domain: `Store/CartHelper`, `Store/CheckoutHelper`, not scattered separately
