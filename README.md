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

## Security Configuration

The package includes a basic security enhancement to prevent arbitrary class instantiation in the `DeleteActionTrait`.

### Configuration

Publish the configuration file to customize allowed models:

```bash
php artisan vendor:publish --tag=orchid-helpers-config
```

Or manually create `config/orchid-helpers.php` with the following structure:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Models for Delete Action
    |--------------------------------------------------------------------------
    |
    | This configuration defines which Eloquent model classes are allowed
    | to be instantiated via the DeleteActionTrait. This is a security
    | measure to prevent arbitrary class instantiation from user input.
    |
    | You MUST explicitly list all model classes that are allowed.
    | An empty array means NO models are allowed (strict security).
    |
    | Only fully qualified class names are supported (e.g., App\Models\User::class).
    | Wildcard patterns are NOT supported for stricter security.
    |
    */
    
    'allowed_models' => [
        // Example:
        // App\Models\User::class,
        // App\Models\Post::class,
    ],
];
```

### Security Feature

**Model Allowlist Validation**: The `DeleteActionTrait` validates that the model class from user input is in the `allowed_models` list before instantiation. For strict security, an empty `allowed_models` array means NO models are allowed. You must explicitly list all allowed model classes.

### Upgrading from Previous Versions

Previous versions allowed all models when `allowed_models` was empty (backward compatibility). The new strict security requires explicit configuration. Update your `config/orchid-helpers.php` to explicitly list all allowed model classes.

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

### Security Testing

The package includes security tests for the `DeleteActionTrait` to validate:
- Model class allowlist validation
- Input parameter validation
- Authorization checks

### Continuous Integration

GitHub Actions workflow (`.github/workflows/test.yml`) automatically runs tests on:
- Push to `main` and `develop` branches
- Pull requests targeting `main` branch
- Multiple PHP versions (8.3, 8.4)
- Multiple Laravel versions (11.*)

### Writing New Tests

When adding new functionality, follow the existing test patterns:

1. **Unit Tests**: Test individual components in isolation
2. **Feature Tests**: Test integration with Laravel and Orchid
3. **Security Tests**: Validate security-critical functionality

Example test structure:
```php
<?php

namespace OrchidHelpers\Tests\Unit;

use OrchidHelpers\Tests\TestCase;

class NewComponentTest extends TestCase
{
    /** @test */
    public function it_performs_expected_behavior()
    {
        // Arrange
        $component = new Component();
        
        // Act
        $result = $component->execute();
        
        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email andrey.manzadey@gmail.com instead of using the issue tracker.

## Credits

- [Andrey Manzadey](https://github.com/manzadey)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.