# Laravel Orchid Helpers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)
[![PHP Version](https://img.shields.io/packagist/php-v/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)
[![License](https://img.shields.io/packagist/l/eliminationzx/laravel-orchid-helpers.svg?style=flat-square)](https://packagist.org/packages/eliminationzx/laravel-orchid-helpers)

A comprehensive collection of helpers, components, and utilities for Laravel Orchid Platform that accelerates admin panel development.

## Features

- **Pre-built Components**: Ready-to-use alerts, buttons, fields, layouts, links, screens, sights, and TD components
- **Type-safe**: Full PHP 8.3+ type declarations
- **Modern Stack**: Compatible with latest Orchid Platform 14.x
- **Laravel Integration**: Seamless integration with Laravel service providers

## Requirements

- PHP 8.3 or higher
- Laravel 10.x or higher
- Orchid Platform 14.53 or higher

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

## Testing

```bash
composer test
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