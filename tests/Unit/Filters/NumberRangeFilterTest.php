<?php

namespace OrchidHelpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OrchidHelpers\Orchid\Filters\NumberRangeFilter;
use OrchidHelpers\Tests\TestCase;

class NumberRangeFilterTest extends TestCase
{
    public function test_it_creates_number_range_filter()
    {
        $filter = new NumberRangeFilter('price');
        
        $this->assertEquals(['price_min', 'price_max'], $filter->parameters());
        $this->assertEquals('Price', $filter->name());
    }

    public function test_it_applies_filter_for_min_and_max_values()
    {
        $filter = new NumberRangeFilter('price');
        
        $builder = $this->createMock(Builder::class);
        
        // First call for min value
        $builder->expects($this->atLeastOnce())
            ->method('where')
            ->with('price', '>=', 100)
            ->willReturn($builder);
        
        // Second call for max value
        $builder->expects($this->atLeastOnce())
            ->method('where')
            ->with('price', '<=', 1000)
            ->willReturn($builder);
        
        $request = new Request([
            'price_min' => 100,
            'price_max' => 1000,
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_min_value_only()
    {
        $filter = new NumberRangeFilter('price');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('price', '>=', 100)
            ->willReturn($builder);
        
        $request = new Request([
            'price_min' => 100,
            'price_max' => null,
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_max_value_only()
    {
        $filter = new NumberRangeFilter('price');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('price', '<=', 1000)
            ->willReturn($builder);
        
        $request = new Request([
            'price_min' => null,
            'price_max' => 1000,
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_does_not_apply_filter_when_no_values_provided()
    {
        $filter = new NumberRangeFilter('price');
        
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
        $filter = new NumberRangeFilter('price');
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $fields[0]);
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $fields[1]);
    }

    public function test_it_has_run_method_that_filters_builder()
    {
        $filter = new NumberRangeFilter('price');
        
        $request = new Request([
            'price_min' => 100,
            'price_max' => 1000,
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $builder = $this->createMock(Builder::class);
        
        // First call for min value
        $builder->expects($this->atLeastOnce())
            ->method('where')
            ->with('price', '>=', 100)
            ->willReturn($builder);
        
        // Second call for max value
        $builder->expects($this->atLeastOnce())
            ->method('where')
            ->with('price', '<=', 1000)
            ->willReturn($builder);
        
        $result = $filter->run($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_accepts_min_max_step_parameters()
    {
        $filter = new NumberRangeFilter('price', 0, 10000, 0.01);
        
        // Test that constructor accepts parameters without error
        $this->assertInstanceOf(NumberRangeFilter::class, $filter);
        
        // Verify parameters method works
        $this->assertEquals(['price_min', 'price_max'], $filter->parameters());
    }
}