# Laravel Dynamic Helpers

[![Latest Version](https://img.shields.io/packagist/v/l0n3ly/laravel-dynamic-helpers.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-dynamic-helpers)
[![Total Downloads](https://img.shields.io/packagist/dt/l0n3ly/laravel-dynamic-helpers.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-dynamic-helpers)
[![License](https://img.shields.io/packagist/l/l0n3ly/laravel-dynamic-helpers.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-dynamic-helpers)

A powerful Laravel package that provides a dynamic helper management system with an Artisan command generator. Create, organize, and access your custom helper classes effortlessly.

## ✨ Features

- 🚀 **Dynamic Helper Resolution** - Automatically resolves helper classes on-demand
- 🎯 **Artisan Command Generator** - Create helpers with `php artisan make:helper`
- 📁 **Nested Helper Support** - Organize helpers in subdirectories (e.g., `Store/CreateHelper`)
- 🔄 **Singleton Pattern** - Efficient instance caching for better performance
- 🎨 **Laravel-Style Output** - Beautiful command output matching Laravel's conventions
- 🔌 **Auto-Discovery** - Service provider automatically registered
- 💡 **Dual Access Patterns** - Use `moneyHelper()` or `helpers()->moneyHelper()`
- 🧠 **IDE Autocompletion** - Generate IDE helper file with `php artisan helpers:ide` for full method autocomplete

## 📋 Requirements

- PHP >= 8.1
- Laravel >= 11.0 (11, 12, 13)

## 📦 Installation

Install the package via Composer:

```bash
composer require l0n3ly/laravel-dynamic-helpers
```

The service provider will be automatically discovered by Laravel.

## 🚀 Quick Start

### 1. Create a Helper

```bash
php artisan make:helper MoneyHelper
```

This creates `app/Helpers/MoneyHelper.php`:

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class MoneyHelper extends Helper
{
    public function format($amount)
    {
        return number_format($amount, 2);
    }

    public function toMinor($amount)
    {
        return $amount * 100;
    }
}
```

### 2. Use Your Helper

You can access your helper in two ways:

```php
// Direct global function (recommended)
moneyHelper()->format(1000); // "1,000.00"
moneyHelper()->toMinor(1500); // 150000

// Via helpers() function
helpers()->moneyHelper()->format(2000); // "2,000.00"
```

## 📚 Usage Examples

### Basic Helper

```bash
php artisan make:helper PermissionHelper
```

```php
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class PermissionHelper extends Helper
{
    public function can($permission)
    {
        return auth()->user()->hasPermission($permission);
    }
}
```

Usage:

```php
if (permissionHelper()->can('edit-posts')) {
    // User can edit posts
}
```

### Nested Helpers

Create organized helper structures:

```bash
php artisan make:helper Store/CreateHelper
php artisan make:helper Store/Product/UpdateHelper
```

This creates:
- `app/Helpers/Store/CreateHelper.php`
- `app/Helpers/Store/Product/UpdateHelper.php`

Access them using flattened camelCase:

```php
// Store/CreateHelper -> storeCreateHelper()
storeCreateHelper()->create($data);

// Store/Product/UpdateHelper -> storeProductUpdateHelper()
storeProductUpdateHelper()->update($id, $data);
```

### In Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $amount = moneyHelper()->toMinor($request->amount);

        if (permissionHelper()->can('create-orders')) {
            // Create order
        }
    }
}
```

### In Blade Templates

```blade
@if(permissionHelper()->can('view-reports'))
    <div class="reports">
        {{ moneyHelper()->format($total) }}
    </div>
@endif
```

## 🎯 Advanced Features

### Instance Caching

Helpers are automatically cached as singletons:

```php
$helper1 = moneyHelper();
$helper2 = moneyHelper();

// $helper1 and $helper2 are the same instance
```

### Callable Helpers

Helpers can be callable:

```php
class CalculatorHelper extends Helper
{
    public function __invoke($a, $b)
    {
        return $a + $b;
    }
}
```

Usage:

```php
$result = calculatorHelper(5, 10); // 15
```

### Custom Helper Methods

Add any methods you need:

```php
class ApiHelper extends Helper
{
    public function get($url)
    {
        return Http::get($url);
    }

    public function post($url, $data)
    {
        return Http::post($url, $data);
    }
}
```

## 💡 IDE Autocompletion

The package includes an IDE helper generator so your editor understands what `helpers()->storeCreateHelper()` returns and can autocomplete methods on it.

### Generate the IDE helper file

```bash
php artisan helpers:ide
```

This creates `_ide_helper_helpers.php` in your project root. The file is automatically added to your `.gitignore` on first run.

> **Note:** `php artisan make:helper` automatically regenerates this file for you. You only need to run it manually when you create or delete helper files without using the artisan command.

### Keep it in sync automatically (recommended)

Add this to your app's `composer.json` so it also regenerates whenever you run `composer install`, `composer update`, or `composer dump-autoload`:

```json
"scripts": {
    "post-autoload-dump": [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan package:discover --ansi",
        "@php artisan helpers:ide --ansi || true"
    ]
}
```

## 📖 Command Reference

### Create a Helper

```bash
php artisan make:helper HelperName
```

### Generate IDE Helper

```bash
php artisan helpers:ide
```

Custom output path:

```bash
php artisan helpers:ide --output=ide_helpers.php
```

### Create Nested Helper

```bash
php artisan make:helper Category/ProductHelper
php artisan make:helper Admin/User/PermissionHelper
```

### Helper Name Normalization

The command automatically normalizes names:

```bash
# All of these create "MoneyHelper"
php artisan make:helper MoneyHelper
php artisan make:helper money-helper
php artisan make:helper money_helper
```

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Or with PHPUnit:

```bash
vendor/bin/phpunit
```

## 📝 Code Style

This package uses [Laravel Pint](https://laravel.com/docs/pint) for code style. Format code:

```bash
./vendor/bin/pint
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.

## 👤 Author

**Divine Idehen**

- Email: idehendivine16@gmail.com

## 🙏 Acknowledgments

- Inspired by Laravel's elegant architecture
- Built with the Laravel community in mind

---

Made with ❤️ for the Laravel community

