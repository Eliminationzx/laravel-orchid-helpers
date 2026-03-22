# Laravel Orchid Helpers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)
[![PHP Version](https://img.shields.io/packagist/php-v/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)
[![License](https://img.shields.io/packagist/l/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)

A comprehensive collection of helpers, components, and utilities for Laravel Orchid Platform that accelerates admin panel development.

## Features

- **Pre-built Components**: Ready-to-use alerts, buttons, fields, layouts, links, screens, sights, and TD components
- **Type-safe**: Full PHP 8.3+ type declarations
- **Modern Stack**: Compatible with latest Orchid Platform 14+ (PHP 8.3+)
- **Laravel Integration**: Seamless integration with Laravel service providers

## Requirements

- PHP 8.3 or higher (with modern PHP features: typed properties, `#[\Override]` attribute, union types, etc.)
- Laravel 10.x, 11.x, 12.x or higher
- Orchid Platform 14.53 or higher (latest version)

## Installation

You can install the package via Composer:

```bash
composer require eliminationzx/laravel-orchid-helpers
```

The package will automatically register its service providers.

## Available Helpers

### 1. Alerts
- `SaveAlert`: Pre-configured save success alert
- `DestroyAlert`: Delete confirmation and success alerts

### 2. Buttons
- `SaveButton`: Standard save button with loading state

### 3. Fields
- `BooleanCheckbox`: Toggle switch for boolean fields

### 4. Layouts
- `ModelLegendLayout`: Display model information in legend format
- `ModelMetricLayout`: Show metrics for models
- `ModelsTableLayout`: Tabular layout for model lists
- `ModelTimestampsLayout`: Display created/updated timestamps

### 5. Links
- `CreateLink`, `EditLink`, `ShowLink`, `DeleteLink`: CRUD operation links
- `DropdownOptions`, `DropdownRelations`: Dropdown navigation components

### 6. Screens
- `AbstractScreen`: Base screen with authorization helpers
- `EditScreen`: Pre-configured edit screen
- `ShowScreen`: Pre-configured show screen

### 7. Sights
- `BoolSight`, `CreatedAtSight`, `UpdatedAtSight`, `IdSight`: Display components
- `EntitySight`, `TimestampSight`, `PrintSight`, `DumpSight`: Various display helpers

### 8. TD (Table Data)
- `ActionsTD`, `BoolTD`, `CountTD`, `CreatedAtTD`, `UpdatedAtTD`: Table cell components
- `EntityTD`, `EntityRelationTD`, `IdTD`, `LinkTD`, `MorphNameTD`: Data display components

### 9. Filters
- `BooleanFilter`, `CreatedTimestampFilter`, `UpdatedTimestampFilter`
- `IdFilter`, `TimestampFilter`, `UserFilter`

### 10. Global Helpers
- `attrName()`: Helper function for attribute name translation

## Usage Example

```php
use OrchidHelpers\Orchid\Helpers\Screens\EditScreen;
use OrchidHelpers\Orchid\Helpers\Layouts\ModelLegendLayout;
use OrchidHelpers\Orchid\Helpers\Fields\BooleanCheckbox;

class UserEditScreen extends EditScreen
{
    public function layout(): array
    {
        return [
            ModelLegendLayout::make($this->model),
            
            Layout::rows([
                BooleanCheckbox::make('user.is_active')
                    ->title('Active')
                    ->sendTrueOrFalse(),
            ]),
        ];
    }
}
```

## Service Providers

The package automatically registers:
- `FoundationServiceProvider`: Core service registration
- `MacrosServiceProvider`: Laravel macro extensions

## Security Feature

The package includes a basic security enhancement to prevent arbitrary class instantiation in the `DeleteActionTrait`.

### Model Validation

The `DeleteActionTrait` includes a protected `isModelAllowed()` method that validates model classes before instantiation. By default, it checks that:

1. The class exists and is a concrete class (not abstract or interface)
2. The class extends `Illuminate\Database\Eloquent\Model`

This provides basic protection against arbitrary class instantiation from user input.

### Customizing Validation

You can override the `isModelAllowed()` method in your screen class to implement custom validation logic:

```php
use OrchidHelpers\Orchid\Traits\DeleteActionTrait;

class UserScreen
{
    use DeleteActionTrait;
    
    protected function isModelAllowed(string $modelClass): bool
    {
        // First, use the parent validation
        if (!parent::isModelAllowed($modelClass)) {
            return false;
        }
        
        // Add your custom logic here
        // Example: Only allow specific models
        $allowedModels = [
            \App\Models\User::class,
            \App\Models\Post::class,
        ];
        
        return in_array($modelClass, $allowedModels);
    }
}
```

## Testing

The package includes a comprehensive testing suite with PHPUnit and Orchestra Testbench for Laravel package testing.

### Running Tests

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run only unit tests
composer test:unit

# Run only feature tests
composer test:feature

# Generate test coverage report
composer test:coverage
```

### Test Structure

```
tests/
├── TestCase.php              # Base test case with Orchestra Testbench setup
├── Feature/                  # Feature tests
│   └── DeleteActionTraitSecurityTest.php  # Security validation tests
└── Unit/                    # Unit tests
    ├── HelpersTest.php      # Helper function tests
    ├── Filters/             # Filter component tests
    │   └── BooleanFilterTest.php
    ├── Fields/              # Field component tests
    │   └── BooleanCheckboxTest.php
    └── Providers/           # Service provider tests
        └── ServiceProviderTest.php
```

### Test Configuration

The testing environment is configured with:
- SQLite in-memory database
- Mockery for mocking dependencies
- Orchestra Testbench for Laravel package testing

## Credits

- [Eliminationzx](https://github.com/eliminationzx)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.