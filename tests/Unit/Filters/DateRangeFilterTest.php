<?php

namespace OrchidHelpers\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OrchidHelpers\Orchid\Filters\DateRangeFilter;
use OrchidHelpers\Tests\TestCase;

class DateRangeFilterTest extends TestCase
{
    public function test_it_creates_date_range_filter()
    {
        $filter = new DateRangeFilter('created_at');
        
        $this->assertEquals('created_at', $filter->parameters()[0]);
        $this->assertEquals('Created At', $filter->name());
    }

    public function test_it_applies_filter_for_date_range()
    {
        $filter = new DateRangeFilter('created_at');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereBetween')
            ->with('created_at', ['2024-01-01', '2024-01-31'])
            ->willReturn($builder);
        
        $request = new Request([
            'created_at' => [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
            ],
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_single_date()
    {
        $filter = new DateRangeFilter('created_at');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereDate')
            ->with('created_at', '2024-01-01')
            ->willReturn($builder);
        
        $request = new Request([
            'created_at' => [
                'start' => '2024-01-01',
                'end' => '2024-01-01',
            ],
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_start_date_only()
    {
        $filter = new DateRangeFilter('created_at');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereDate')
            ->with('created_at', '>=', '2024-01-01')
            ->willReturn($builder);
        
        $request = new Request([
            'created_at' => [
                'start' => '2024-01-01',
                'end' => null,
            ],
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_applies_filter_for_end_date_only()
    {
        $filter = new DateRangeFilter('created_at');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereDate')
            ->with('created_at', '<=', '2024-01-31')
            ->willReturn($builder);
        
        $request = new Request([
            'created_at' => [
                'start' => null,
                'end' => '2024-01-31',
            ],
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $result = $filter->filter($builder);
        $this->assertSame($builder, $result);
    }

    public function test_it_does_not_apply_filter_when_no_dates_provided()
    {
        $filter = new DateRangeFilter('created_at');
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->never())
            ->method('whereBetween');
        $builder->expects($this->never())
            ->method('whereDate');
        
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
        $filter = new DateRangeFilter('created_at');
        
        $fields = $filter->display();
        
        $this->assertIsArray($fields);
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(\Orchid\Screen\Fields\DateRange::class, $fields[0]);
    }

    public function test_it_has_run_method_that_filters_builder()
    {
        $filter = new DateRangeFilter('created_at');
        
        $request = new Request([
            'created_at' => [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
            ],
        ]);
        
        $reflection = new \ReflectionClass($filter);
        $requestProperty = $reflection->getProperty('request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($filter, $request);
        
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('whereBetween')
            ->with('created_at', ['2024-01-01', '2024-01-31'])
            ->willReturn($builder);
        
        $result = $filter->run($builder);
        $this->assertSame($builder, $result);
    }
}