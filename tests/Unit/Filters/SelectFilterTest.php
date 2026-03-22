<?php

namespace Orchid\Helpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orchid\Helpers\Orchid\Filters\SelectFilter;
use Orchid\Helpers\Tests\TestCase;

class SelectFilterTest extends TestCase
{
    public function test_it_creates_select_filter_with_options()
    {
        $options = ['option1' => 'Option 1', 'option2' => 'Option 2'];
        $filter = new SelectFilter('category', $options);
        
        $this->assertEquals('category', $filter->parameters()[0]);
        $this->assertEquals('Category', $filter->name());
    }

    public function test_it_applies_filter_for_single_value()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new SelectFilter('status', $options);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('status', 'active')
            ->willReturn($builder);
        
        $request = new Request(['status' => 'active']);
        
        // Set request on filter using reflection
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_multiple_values()
    {
        $options = ['cat1' => 'Category 1', 'cat2' => 'Category 2'];
        $filter = new SelectFilter('categories', $options, true);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereIn')
            ->with('categories', ['cat1', 'cat2'])
            ->willReturn($builder);
        
        $request = new Request(['categories' => ['cat1', 'cat2']]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_does_not_apply_filter_when_value_not_provided()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new SelectFilter('status', $options);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->never())
            ->method('where');
        
        $request = new Request([]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_returns_correct_display_fields()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new SelectFilter('status', $options);
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Select::class, $fields[0]);
    }

    public function test_it_has_run_method_that_filters_builder()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new SelectFilter('status', $options);
        
        // Mock request with input using reflection to set protected property
        $request = new Request(['status' => 'active']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('status', 'active')
            ->willReturn($builder);
        
        $result = $filter->run($builder);
        $this->assertSame($builder, $result);
    }
}
