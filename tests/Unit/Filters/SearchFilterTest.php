<?php

namespace OrchidHelpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OrchidHelpers\Orchid\Filters\SearchFilter;
use OrchidHelpers\Tests\TestCase;

class SearchFilterTest extends TestCase
{
    public function test_it_creates_search_filter()
    {
        $filter = new SearchFilter('name');
        
        $this->assertEquals('name', $filter->parameters()[0]);
        $this->assertEquals('Name', $filter->name());
    }

    public function test_it_applies_filter_with_like_search()
    {
        $filter = new SearchFilter('name');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('name', 'LIKE', '%test%')
            ->willReturn($builder);
        
        $request = new Request(['name' => 'test']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_with_exact_match()
    {
        $filter = new SearchFilter('name', true);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('name', 'test')
            ->willReturn($builder);
        
        $request = new Request(['name' => 'test']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_does_not_apply_filter_when_value_empty()
    {
        $filter = new SearchFilter('name');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->never())
            ->method('where');
        
        $request = new Request(['name' => '']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_does_not_apply_filter_when_value_not_provided()
    {
        $filter = new SearchFilter('name');
        
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
        $filter = new SearchFilter('name');
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $fields[0]);
    }

    public function test_it_has_run_method_that_filters_builder()
    {
        $filter = new SearchFilter('name');
        
        $request = new Request(['name' => 'test']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('name', 'LIKE', '%test%')
            ->willReturn($builder);
        
        $result = $filter->run($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_trims_search_value()
    {
        $filter = new SearchFilter('name');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('name', 'LIKE', '%test%')
            ->willReturn($builder);
        
        $request = new Request(['name' => '  test  ']);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }
}