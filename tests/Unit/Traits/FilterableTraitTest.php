<?php

namespace Orchid\Helpers\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Orchid\Helpers\Orchid\Traits\FilterableTrait;
use Orchid\Helpers\Tests\TestCase;

class FilterableTraitTest extends TestCase
{
    /** @test */
    public function it_applies_equals_filter()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'status'];
            protected $filterable = [
                'name' => 'equals',
                'status' => 'equals',
            ];
        };

        $request = Request::create('/', 'GET', ['name' => 'John', 'status' => 'active']);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('name', $filteredQuery->toSql());
        $this->assertStringContainsString('status', $filteredQuery->toSql());
    }

    /** @test */
    public function it_applies_contains_filter()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'description'];
            protected $filterable = [
                'name' => 'contains',
                'description' => 'contains',
            ];
        };

        $request = Request::create('/', 'GET', ['name' => 'John']);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('LIKE', $filteredQuery->toSql());
        $this->assertStringContainsString('%John%', $filteredQuery->toSql());
    }

    /** @test */
    public function it_applies_greater_than_filter()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['age', 'price'];
            protected $filterable = [
                'age' => 'greater_than',
                'price' => 'greater_than',
            ];
        };

        $request = Request::create('/', 'GET', ['age' => '18']);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('age', $filteredQuery->toSql());
        $this->assertStringContainsString('>', $filteredQuery->toSql());
    }

    /** @test */
    public function it_applies_between_filter()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['price'];
            protected $filterable = [
                'price' => 'between',
            ];
        };

        $request = Request::create('/', 'GET', ['price' => [100, 200]]);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('price', $filteredQuery->toSql());
        $this->assertStringContainsString('between', strtolower($filteredQuery->toSql()));
    }

    /** @test */
    public function it_applies_in_filter()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['status'];
            protected $filterable = [
                'status' => 'in',
            ];
        };

        $request = Request::create('/', 'GET', ['status' => ['active', 'pending']]);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('status', $filteredQuery->toSql());
        $this->assertStringContainsString('in', strtolower($filteredQuery->toSql()));
    }

    /** @test */
    public function it_ignores_empty_filter_values()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'status'];
            protected $filterable = [
                'name' => 'equals',
                'status' => 'equals',
            ];
        };

        $request = Request::create('/', 'GET', ['name' => '', 'status' => null]);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        // Should not add where clauses for empty values
        $this->assertStringNotContainsString('where', $filteredQuery->toSql());
    }

    /** @test */
    public function it_uses_default_filter_type_when_not_specified()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'status'];
            protected $filterable = ['name', 'status']; // No filter type specified
        };

        $request = Request::create('/', 'GET', ['name' => 'John']);
        
        $query = $model->newQuery();
        $filteredQuery = $model->scopeFilter($query, $request);
        
        // Should default to 'equals' filter type
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('name', $filteredQuery->toSql());
        $this->assertStringContainsString('=', $filteredQuery->toSql());
    }

    /** @test */
    public function it_returns_filter_options()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'status', 'price'];
            protected $filterable = [
                'name' => 'contains',
                'status' => 'equals',
                'price' => 'greater_than',
            ];
        };

        $options = $model::getFilterOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('name', $options);
        $this->assertArrayHasKey('status', $options);
        $this->assertArrayHasKey('price', $options);
        
        $this->assertEquals('contains', $options['name']['type']);
        $this->assertEquals('equals', $options['status']['type']);
        $this->assertEquals('greater_than', $options['price']['type']);
        
        $this->assertEquals('Name', $options['name']['label']);
        $this->assertEquals('Status', $options['status']['label']);
        $this->assertEquals('Price', $options['price']['label']);
    }

    /** @test */
    public function it_applies_quick_search()
    {
        $model = new class extends Model {
            use FilterableTrait;
            
            protected $fillable = ['name', 'email', 'description'];
            protected $searchable = ['name', 'email'];
        };

        $query = $model->newQuery();
        $searchQuery = $model->scopeQuickSearch($query, 'john', ['name', 'email']);
        
        $this->assertStringContainsString('where', $searchQuery->toSql());
        $this->assertStringContainsString('name', $searchQuery->toSql());
        $this->assertStringContainsString('email', $searchQuery->toSql());
        $this->assertStringContainsString('LIKE', $searchQuery->toSql());
        $this->assertStringContainsString('%john%', $searchQuery->toSql());
    }

    /** @test */
    public function it_handles_custom_filter_callbacks()
    {
        $callback = function (Builder $query, string $column, $value) {
            $query->where($column, 'LIKE', "{$value}%");
        };
        
        $model = new class($callback) extends Model {
            use FilterableTrait;
            
            private $callback;
            
            public function __construct($callback)
            {
                parent::__construct();
                $this->callback = $callback;
                $this->filterable = [
                    'name' => $this->callback,
                ];
            }
            
            protected $fillable = ['name'];
        };

        $modelInstance = new $model($callback);
        $request = Request::create('/', 'GET', ['name' => 'John']);
        
        $query = $modelInstance->newQuery();
        $filteredQuery = $modelInstance->scopeFilter($query, $request);
        
        $this->assertStringContainsString('where', $filteredQuery->toSql());
        $this->assertStringContainsString('name', $filteredQuery->toSql());
        $this->assertStringContainsString('LIKE', $filteredQuery->toSql());
        $this->assertStringContainsString('John%', $filteredQuery->toSql());
    }
}
