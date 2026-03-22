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
- **New Alert Helpers**:
  - `SuccessAlert`: Success notifications for successful operations
  - `ErrorAlert`: Error notifications for failed operations
  - `WarningAlert`: Warning notifications for attention-required situations
  - `InfoAlert`: Informational notifications for general information
  - `ToastAlert`: Toast/popup notifications with customizable types
  - `BannerAlert`: Banner notifications for prominent displays
  - `InlineAlert`: Inline form validation alerts with field-specific messages
  - `ConfirmationAlert`: Confirmation dialog alerts with action buttons
  - `ProgressAlert`: Progress/loading notifications with completion tracking
  - `TimedAlert`: Auto-dismissing notifications with configurable timeout
  - `DismissibleAlert`: Dismissible notifications with close functionality
  - `ActionAlert`: Alerts with action buttons for user interactions
  - `StatusAlert`: Status change notifications with before/after states
  - `SystemAlert`: System/technical notifications with error codes

### 2. Buttons
- `SaveButton`: Standard save button with loading state
- **New Button Helpers**:
  - `SubmitButton`: For form submissions with primary styling
  - `CancelButton`: For cancel actions with secondary styling
  - `DeleteButton`: For delete actions with confirmation dialog and danger styling
  - `EditButton`: For edit actions with primary styling
  - `ViewButton`: For view/details actions with info styling
  - `AddButton`: For add/create actions with success styling
  - `BackButton`: For navigation back with secondary styling
  - `NextButton`: For next step actions with primary styling
  - `PreviousButton`: For previous step actions with secondary styling
  - `DownloadButton`: For download actions with success styling
  - `PrintButton`: For print actions with default styling
  - `ExportButton`: For export actions with info styling
  - `ImportButton`: For import actions with warning styling
  - `RefreshButton`: For refresh/reload actions with default styling
  - `SearchButton`: For search actions with info styling
  - `FilterButton`: For filter actions with secondary styling
  - `SortButton`: For sort actions with default styling
  - `ToggleButton`: For toggle actions with default styling
  - `CopyButton`: For copy to clipboard actions with info styling
  - `ShareButton`: For share actions with info styling

### 3. Fields
- `BooleanCheckbox`: Toggle switch for boolean fields
- **New Field Helpers**:
  - `TextField`: Standard text input field with validation support
  - `EmailField`: Email input field with email validation
  - `PasswordField`: Password input field with toggle visibility support
  - `NumberField`: Numeric input field with min/max constraints
  - `TextareaField`: Multi-line text input field
  - `SelectField`: Dropdown selection field
  - `DateField`: Date picker input field
  - `DateTimeField`: Date-time picker input field
  - `FileField`: File upload field
  - `ImageField`: Image upload field with preview
  - `CheckboxField`: Single checkbox input field
  - `RadioField`: Radio button group field
  - `ColorField`: Color picker input field
  - `RichTextField`: Rich text/WYSIWYG editor field

### 4. Layouts
- `ModelLegendLayout`: Display model information in legend format
- `ModelMetricLayout`: Show metrics for models
- `ModelsTableLayout`: Tabular layout for model lists
- `ModelTimestampsLayout`: Display created/updated timestamps
- **New Layout Helpers**:
  - `CardLayout`: Card-based content containers with title and content
  - `TabLayout`: Tabbed interface layouts with optional icons
  - `AccordionLayout`: Collapsible accordion sections for organized content
  - `GridLayout`: Responsive grid layouts with automatic column management
  - `FormLayout`: Standardized form layouts with inline and section support
  - `DashboardLayout`: Dashboard/metrics layouts with charts and tables
  - `EmptyStateLayout`: Empty state displays for tables and search results
  - `LoadingLayout`: Loading state displays with spinners and skeletons
  - `ErrorLayout`: Error state displays for various error types
  - `ModalLayout`: Modal dialog layouts with confirmation and form support

### 5. Links
- `CreateLink`, `EditLink`, `ShowLink`, `DeleteLink`: CRUD operation links
- `DropdownOptions`, `DropdownRelations`: Dropdown navigation components
- **New Navigation Link Helpers**:
  - `BackLink`: For going back to previous page with history navigation
  - `HomeLink`: For navigating to home/dashboard with house icon
  - `ExternalLink`: For external URLs with new tab opening and security attributes
  - `DownloadLink`: For file downloads with download attribute
  - `PrintLink`: For print functionality with print dialog
  - `RefreshLink`: For refreshing current page with reload functionality
  - `ToggleLink`: For toggle/switch actions with activation/deactivation states
  - `CopyLink`: For copying text to clipboard with success feedback
  - `ShareLink`: For sharing content on social media (Twitter, Facebook, LinkedIn)
  - `BreadcrumbLink`: For breadcrumb navigation with active state support
  - `PaginationLink`: For pagination navigation (previous, next, first, last, page numbers)
  - `SortLink`: For sortable column headers with ascending/descending indicators
  - `FilterLink`: For filter toggles with active state and clear filters option
  - `ModalLink`: For opening modals with various sizes and async functionality

### 6. Screens
- `AbstractScreen`: Base screen with authorization helpers
- `EditScreen`: Pre-configured edit screen
- `ShowScreen`: Pre-configured show screen
- **New Screen Helpers**:
  - `ListScreen`: For listing/table views with pagination, sorting, and filtering
  - `CreateScreen`: For creating new records with form validation
  - `UpdateScreen`: For updating existing records
  - `DeleteScreen`: For deletion with confirmation dialogs
  - `ImportScreen`: For bulk import operations with file upload
  - `ExportScreen`: For data export functionality in multiple formats
  - `BulkActionScreen`: For bulk operations on multiple records
  - `SearchScreen`: For advanced search interfaces with filters
  - `ReportScreen`: For reporting and analytics views with charts
  - `DashboardScreen`: For dashboard/home screens with metrics and widgets

### 7. Sights
- `BoolSight`, `CreatedAtSight`, `UpdatedAtSight`, `IdSight`: Display components
- `EntitySight`, `TimestampSight`, `PrintSight`, `DumpSight`: Various display helpers
- **New Sight Helpers**:
  - `TextSight`: Text data display with optional truncation and tooltips
  - `EmailSight`: Email addresses with mailto links and envelope icon
  - `PhoneSight`: Phone numbers with tel links and phone icon
  - `UrlSight`: URLs with clickable links and external link icon
  - `ImageSight`: Image display with configurable dimensions and styling
  - `AvatarSight`: Avatar/profile image display with circular styling and initials fallback
  - `BadgeSight`: Status badges with automatic color coding based on values
  - `ProgressSight`: Progress bars with customizable colors and labels
  - `RatingSight`: Star ratings with configurable star count and colors
  - `CurrencySight`: Formatted currency values with configurable symbols and decimals
  - `PercentageSight`: Percentage values with formatting options and symbol
  - `DateSight`: Formatted date display with customizable format and timezone
  - `DateTimeSight`: Formatted date-time display with customizable format
  - `JsonSight`: JSON data display with collapsible formatting and syntax highlighting
  - `CodeSight`: Code syntax highlighting with line numbers and language support
  - `MarkdownSight`: Markdown rendering with HTML sanitization
  - `HtmlSight`: HTML content rendering with configurable sanitization
  - `FileSizeSight`: Human-readable file sizes with automatic unit conversion
  - `DurationSight`: Time duration display with multiple format options
  - `CountSight`: Count/number display with optional badge styling

### 8. TD (Table Data)
- `ActionsTD`, `BoolTD`, `CountTD`, `CreatedAtTD`, `UpdatedAtTD`: Table cell components
- `EntityTD`, `EntityRelationTD`, `IdTD`, `LinkTD`, `MorphNameTD`: Data display components
- **New TD Helpers**:
  - `EmailTD`: Email addresses with mailto links
  - `PhoneTD`: Phone numbers with tel links
  - `CurrencyTD`: Formatted currency values with configurable symbols
  - `PercentageTD`: Percentage values with formatting options
  - `BadgeTD`: Status badges with automatic color coding
  - `ImageTD`: Images/avatars with configurable dimensions
  - `DateTD`: Formatted date display with customizable format
  - `DateTimeTD`: Formatted date-time display
  - `TruncatedTextTD`: Long text with truncation and tooltips
  - `JsonTD`: JSON data display with collapsible formatting

### 9. Filters
- `BooleanFilter`, `CreatedTimestampFilter`, `UpdatedTimestampFilter`
- `IdFilter`, `TimestampFilter`, `UserFilter`
- `SelectFilter`: Dropdown selection filter with single or multiple options
- `DateRangeFilter`: Date range filtering with flexible date selection
- `NumberRangeFilter`: Numeric range filtering with min/max inputs
- `SearchFilter`: Text search filtering with exact or partial match
- `StatusFilter`: Status/enum filtering with predefined common options

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

The package automatically registers a comprehensive set of service providers for various Laravel components:

### Core Providers
- `FoundationServiceProvider`: Core service registration (helpers, views)
- `MacrosServiceProvider`: Laravel macro extensions for Orchid components and core Laravel classes

#### Available Macros

The MacrosServiceProvider now includes comprehensive macro helpers for extending core Laravel classes:

**Collection Macros:**
- `toUpper()`: Convert all string values to uppercase
- `toLower()`: Convert all string values to lowercase
- `pluckMultiple()`: Pluck multiple keys from each item
- `groupByMultiple()`: Group by multiple keys
- `filterBy()`: Filter collection with operator support
- `sortByMultiple()`: Sort by multiple keys
- `paginate()`: Paginate collection results
- `toCsv()`: Convert collection to CSV format

**String Macros:**
- `toTitleCase()`: Convert string to title case
- `toSnakeCase()`: Convert string to snake_case
- `toCamelCase()`: Convert string to camelCase
- `toPascalCase()`: Convert string to PascalCase
- `truncate()`: Truncate string with ellipsis
- `words()`: Limit string by word count
- `mask()`: Mask parts of a string
- `containsAll()`: Check if string contains all needles
- `containsAny()`: Check if string contains any needle
- `isJson()`: Check if string is valid JSON
- `isBase64()`: Check if string is valid Base64

**Array Macros:**
- `dotFlatten()`: Flatten array with dot notation
- `undot()`: Convert dot notation array to multidimensional
- `exceptNull()`: Remove null values from array
- `exceptEmpty()`: Remove empty values from array
- `pluckMultiple()`: Pluck multiple keys from array of items
- `mapWithKeys()`: Map array with key preservation
- `groupBy()`: Group array by key
- `sortByMultiple()`: Sort array by multiple keys
- `toCsv()`: Convert array to CSV format

**Query Builder Macros:**
- `whereLike()`: Add WHERE LIKE clause
- `orWhereLike()`: Add OR WHERE LIKE clause
- `whereJsonContains()`: Add WHERE JSON_CONTAINS clause
- `whereJsonLength()`: Add WHERE JSON_LENGTH clause
- `filter()`: Apply multiple filters dynamically
- `search()`: Search across multiple columns

**Additional Core Class Macros:**
- **Model Macros**: Extend Eloquent Model functionality
- **Response Macros**: Extend HTTP Response class
- **Request Macros**: Extend HTTP Request class
- **Route Macros**: Extend Route facade
- **View Macros**: Extend View facade
- **File Macros**: Extend File facade
- **Storage Macros**: Extend Storage facade
- **Validator Macros**: Extend Validator class
- **Schema Macros**: Extend Schema builder
- **DB Macros**: Extend DB facade
- **Cache Macros**: Extend Cache class
- **Session Macros**: Extend Session class
- **Cookie Macros**: Extend Cookie class
- **Mail Macros**: Extend Mail class
- **Queue Macros**: Extend Queue class
- **Event Macros**: Extend Event class
- **Log Macros**: Extend Log class
- **Config Macros**: Extend Config class
- **URL Macros**: Extend URL class
- **HTML Macros**: Extend HTML class
- **Form Macros**: Extend Form class
- **Paginator Macros**: Extend Paginator
- **Carbon Macros**: Extend Carbon dates

### Component Providers
- `ComponentServiceProvider`: Blade component registration
- `ViewServiceProvider`: View and view composer registration
- `RouteServiceProvider`: Route registration and configuration
- `EventServiceProvider`: Event and listener registration
- `CommandServiceProvider`: Console command registration
- `MigrationServiceProvider`: Migration and seeder registration
- `ValidationServiceProvider`: Custom validation rule registration
- `HelperServiceProvider`: Helper function registration
- `ConfigServiceProvider`: Configuration merging and publishing

### Infrastructure Providers
- `MiddlewareServiceProvider`: Middleware registration
- `PolicyServiceProvider`: Policy and gate registration
- `RepositoryServiceProvider`: Repository pattern implementation
- `ServiceServiceProvider`: Service class registration
- `FacadeServiceProvider`: Facade registration

### System Providers
- `CacheServiceProvider`: Cache driver registration
- `QueueServiceProvider`: Queue configuration
- `MailServiceProvider`: Mail driver registration
- `NotificationServiceProvider`: Notification channel registration
- `BroadcastServiceProvider`: Broadcasting configuration
- `AuthServiceProvider`: Authentication configuration
- `DatabaseServiceProvider`: Database connection registration
- `FilesystemServiceProvider`: Filesystem disk registration
- `SessionServiceProvider`: Session driver registration
- `TranslationServiceProvider`: Translation and localization

Each provider follows Laravel's service provider patterns and can be extended or disabled as needed. The providers are designed to work independently, allowing you to use only the components you need.

## Trait Helpers

The package includes a comprehensive collection of reusable traits for common functionality in Laravel Orchid applications. These traits follow consistent patterns and provide ready-to-use functionality.

### Available Traits

#### 1. **DeleteActionTrait** (Existing)
- Secure model deletion with authorization and validation
- Built-in security against arbitrary class instantiation
- Redirect handling and success notifications

#### 2. **FilterableTrait**
- Advanced filtering capabilities for Eloquent models
- Supports multiple filter types: equals, contains, greater than, between, in, etc.
- Request-based filtering with customizable filterable columns
- Quick search across multiple columns

#### 3. **SortableTrait**
- Flexible sorting functionality for Eloquent models
- Supports multiple sorting criteria and directions
- Natural sorting and case-insensitive sorting
- UI-friendly sorting options and toggle functionality

#### 4. **SearchableTrait**
- Comprehensive search functionality across model columns
- Multiple search types: contains, exact, fuzzy, fulltext, regex, etc.
- Advanced search with multiple criteria
- Search suggestions and highlighting

#### 5. **AuthorizableTrait**
- Authorization helpers for policies and gates
- Model-specific permission checking
- Role and permission validation (Spatie Laravel Permission compatible)
- Batch authorization and policy scope integration

#### 6. **AuditableTrait**
- Automatic audit logging for model changes
- Tracks created, updated, deleted, and restored events
- Filters sensitive data (passwords, tokens, etc.)
- Custom audit events and change tracking

#### 7. **CacheableTrait**
- Intelligent caching for model queries and relationships
- Cache key generation and TTL management
- Tag-based cache invalidation
- Batch caching and cache statistics

#### 8. **ExportableTrait**
- Data export to multiple formats: CSV, Excel, JSON, XML, PDF
- Customizable columns and transformations
- Request-based export with filtering and sorting
- Excel export with PhpSpreadsheet integration

#### 9. **ImportableTrait**
- Data import from multiple formats: CSV, Excel, JSON, XML
- Field mapping and validation
- Batch processing and error handling
- Update existing records or create new ones

#### 10. **ValidatableTrait**
- Model validation with automatic saving validation
- Scenario-based validation rules
- Request validation helpers
- Validation summary for UI components

#### 11. **TranslatableTrait**
- Multi-language support for model fields
- JSON-based translation storage
- Locale-specific field access
- Translation management and fallbacks

#### 12. **SoftDeletesTrait**
- Enhanced soft delete functionality
- Force delete and restore operations
- Trash management and statistics
- Event hooks for restoring and force deleting

### Using Traits

Traits can be used in your Eloquent models or Orchid screens:

```php
use OrchidHelpers\Orchid\Traits\FilterableTrait;
use OrchidHelpers\Orchid\Traits\SortableTrait;
use OrchidHelpers\Orchid\Traits\SearchableTrait;

class Product extends Model
{
    use FilterableTrait, SortableTrait, SearchableTrait;
    
    // Define filterable columns
    protected $filterable = [
        'name' => 'contains',
        'price' => 'between',
        'status' => 'in',
    ];
    
    // Define sortable columns
    protected $sortable = ['name', 'price', 'created_at'];
    
    // Define searchable columns
    protected $searchable = ['name', 'description', 'sku'];
}

// In your controller or screen
$products = Product::filter($request)
    ->sort($request)
    ->search($request)
    ->paginate();
```

### Configuration

Most traits support configuration through model properties:

```php
class User extends Model
{
    use AuditableTrait, CacheableTrait;
    
    // AuditableTrait configuration
    protected $sensitiveFields = ['password', 'api_token'];
    protected $auditModel = App\Models\AuditLog::class;
    
    // CacheableTrait configuration
    protected $cacheTtl = 3600; // 1 hour
    protected $cacheTags = ['users'];
}
```

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
    ├── Alerts/              # Alert component tests
    │   └── AlertHelpersTest.php  # Tests for new alert helpers
    ├── Buttons/             # Button component tests
    │   └── ButtonHelpersTest.php  # Tests for new button helpers
    ├── Filters/             # Filter component tests
    │   └── BooleanFilterTest.php
    ├── Fields/              # Field component tests
    │   └── BooleanCheckboxTest.php
    ├── Layouts/             # Layout component tests
    │   └── LayoutHelpersTest.php  # Tests for new layout helpers
    ├── Links/               # Link component tests
    │   └── LinkHelpersTest.php    # Tests for new link helpers
    ├── Screens/             # Screen component tests
    │   └── ScreenHelpersTest.php  # Tests for new screen helpers
    ├── TD/                  # TD component tests
    │   └── NewTDTests.php   # Tests for new TD helpers
    ├── Providers/           # Service provider tests
    │   └── ServiceProviderTest.php
    └── Traits/              # Trait helper tests
        └── FilterableTraitTest.php  # Tests for FilterableTrait
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