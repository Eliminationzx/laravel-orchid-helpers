<?php

namespace OrchidHelpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OrchidHelpers\Orchid\Filters\StatusFilter;
use OrchidHelpers\Tests\TestCase;

class StatusFilterTest extends TestCase
{
    public function test_it_creates_status_filter_with_default_options()
    {
        $filter = new StatusFilter();
        
        $this->assertEquals('status', $filter->parameters()[0]);
        $this->assertEquals('Status', $filter->name());
    }

    public function test_it_creates_status_filter_with_custom_field()
    {
        $filter = new StatusFilter('state');
        
        $this->assertEquals('state', $filter->parameters()[0]);
        $this->assertEquals('State', $filter->name());
    }

    public function test_it_creates_status_filter_with_custom_options()
    {
        $options = ['draft' => 'Draft', 'published' => 'Published'];
        $filter = new StatusFilter('status', $options);
        
        $this->assertInstanceOf(StatusFilter::class, $filter);
    }

    public function test_it_applies_filter_for_status_value()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new StatusFilter('status', $options);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('status', 'active')
            ->willReturn($builder);
        
        $request = new Request(['status' => 'active']);
        
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
        $filter = new StatusFilter('status', $options);
        
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

    public function test_it_does_not_apply_filter_when_value_empty()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new StatusFilter('status', $options);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->never())
            ->method('where');
        
        $request = new Request(['status' => '']);
        
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
        $filter = new StatusFilter('status', $options);
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Select::class, $fields[0]);
    }

    public function test_it_has_run_method_that_filters_builder()
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $filter = new StatusFilter('status', $options);
        
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

    public function test_it_uses_default_options_when_none_provided()
    {
        $filter = new StatusFilter();
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Select::class, $fields[0]);
    }
}