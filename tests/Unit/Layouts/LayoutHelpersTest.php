<?php

namespace OrchidHelpers\Tests\Unit\Layouts;

use OrchidHelpers\Orchid\Helpers\Layouts\CardLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\TabLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\AccordionLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\GridLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\FormLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\DashboardLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\EmptyStateLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\LoadingLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\ErrorLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\ModalLayout;
use OrchidHelpers\Tests\TestCase;

class LayoutHelpersTest extends TestCase
{
    public function test_card_layout_make_returns_array()
    {
        $result = CardLayout::make('Test Title', [['type' => 'text', 'content' => 'Test content']]);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_card_layout_blank_returns_array()
    {
        $result = CardLayout::blank([['type' => 'text', 'content' => 'Test content']]);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_tab_layout_make_returns_array()
    {
        $tabs = [
            'tab1' => ['layout' => []],
            'tab2' => ['layout' => []],
        ];
        
        $result = TabLayout::make($tabs);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_tab_layout_with_icons_returns_array()
    {
        $tabs = [
            'tab1' => ['icon' => 'icon-home', 'layout' => []],
            'tab2' => ['icon' => 'icon-user', 'layout' => []],
        ];
        
        $result = TabLayout::withIcons($tabs);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_accordion_layout_make_returns_array()
    {
        $items = [
            ['title' => 'Item 1', 'content' => [], 'expanded' => false],
            ['title' => 'Item 2', 'content' => [], 'expanded' => true],
        ];
        
        $result = AccordionLayout::make($items);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_accordion_layout_item_returns_array()
    {
        $result = AccordionLayout::item('Test Title', ['content'], true);
        
        $this->assertIsArray($result);
        $this->assertEquals('Test Title', $result['title']);
        $this->assertTrue($result['expanded']);
    }

    public function test_grid_layout_make_returns_array()
    {
        $columns = [['content'], ['content']];
        
        $result = GridLayout::make($columns);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_grid_layout_auto_returns_array()
    {
        $columns = [['col1'], ['col2'], ['col3'], ['col4'], ['col5']];
        
        $result = GridLayout::auto($columns, 2);
        
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function test_form_layout_make_returns_array()
    {
        $fields = [['field1'], ['field2']];
        
        $result = FormLayout::make($fields);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_form_layout_inline_returns_array()
    {
        $fields = [['field1'], ['field2'], ['field3']];
        
        $result = FormLayout::inline($fields, 2);
        
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function test_dashboard_layout_make_returns_array()
    {
        $metrics = ['metric1' => 'Value 1', 'metric2' => 'Value 2'];
        $charts = [['chart1'], ['chart2']];
        $tables = [['key' => 'table1', 'columns' => []]];
        
        $result = DashboardLayout::make($metrics, $charts, $tables);
        
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function test_dashboard_layout_metrics_only_returns_array()
    {
        $metrics = ['metric1', 'metric2', 'metric3'];
        
        $result = DashboardLayout::metricsOnly($metrics);
        
        $this->assertIsArray($result);
    }

    public function test_empty_state_layout_make_returns_array()
    {
        $result = EmptyStateLayout::make('No Data', 'There is no data to display');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_empty_state_layout_for_table_returns_array()
    {
        $result = EmptyStateLayout::forTable('users');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_empty_state_layout_for_search_returns_array()
    {
        $result = EmptyStateLayout::forSearch('test query');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_loading_layout_make_returns_array()
    {
        $result = LoadingLayout::make('Loading...');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_loading_layout_spinner_returns_array()
    {
        $result = LoadingLayout::spinner('Please wait');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_loading_layout_skeleton_returns_array()
    {
        $result = LoadingLayout::skeleton(5);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_error_layout_make_returns_array()
    {
        $result = ErrorLayout::make('Error', 'Something went wrong');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_error_layout_not_found_returns_array()
    {
        $result = ErrorLayout::notFound('user');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_error_layout_unauthorized_returns_array()
    {
        $result = ErrorLayout::unauthorized();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_modal_layout_make_returns_array()
    {
        $content = [['type' => 'text', 'content' => 'Modal content']];
        
        $result = ModalLayout::make('Test Modal', $content);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_modal_layout_confirm_returns_array()
    {
        $result = ModalLayout::confirm('Confirm Action', 'Are you sure?');
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_modal_layout_with_form_returns_array()
    {
        $fields = [['field1'], ['field2']];
        
        $result = ModalLayout::withForm('Form Modal', $fields);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }
}