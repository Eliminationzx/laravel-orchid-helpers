<?php

namespace OrchidHelpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OrchidHelpers\Orchid\Filters\BooleanFilter;
use OrchidHelpers\Tests\TestCase;

class BooleanFilterTest extends TestCase
{
    public function test_it_creates_boolean_filter_with_custom_labels()
    {
        $filter = new BooleanFilter('is_active', 'No', 'Yes');
        
        $this->assertEquals('is_active', $filter->parameters()[0]);
        $this->assertEquals('Is Active', $filter->name());
    }
    
    public function test_it_applies_filter_for_true_value()
    {
        $filter = new BooleanFilter('is_active');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('is_active', true)
            ->willReturn($builder);
        
        $request = new Request([$filter->parameters()[0] => '1']);
        
        // Set request on filter using reflection
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }
    
    public function test_it_applies_filter_for_false_value()
    {
        $filter = new BooleanFilter('is_active');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('is_active', false)
            ->willReturn($builder);
        
        $request = new Request([$filter->parameters()[0] => '0']);
        
        // Set request on filter using reflection
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }
    
    public function test_it_does_not_apply_filter_when_value_not_provided()
    {
        $filter = new BooleanFilter('is_active');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->never())
            ->method('where');
        
        $request = new Request([]);
        
        // Set request on filter using reflection
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }
    
    public function test_it_returns_correct_display_fields()
    {
        $filter = new BooleanFilter('is_active', 'Disabled', 'Enabled');
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\RadioButtons::class, $fields[0]);
    }
    
    public function test_it_has_run_method_that_filters_builder()
    {
        $filter = new BooleanFilter('is_active');
        
        // Mock request with input using reflection to set protected property
        $request = new Request(['is_active' => '1']);
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('is_active', true)
            ->willReturn($builder);
        
        $result = $filter->run($builder);
        $this->assertSame($builder, $result);
    }
}