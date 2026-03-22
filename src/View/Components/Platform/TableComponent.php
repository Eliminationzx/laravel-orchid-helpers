<?php

declare(strict_types=1);

namespace Orchid\Helpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class TableComponent extends Component
{
    /**
     * @param array|Collection $data
     * @param array $columns
     * @param array $options
     */
    public function __construct(
        readonly public array|Collection $data = [],
        readonly public array $columns = [],
        readonly public array $options = [],
    ) {
        // Set default options
        $this->options = array_merge([
            'striped' => true,
            'bordered' => false,
            'hover' => true,
            'responsive' => true,
            'small' => false,
            'header' => true,
            'footer' => false,
            'emptyText' => __('No data available'),
            'actions' => [],
            'rowActions' => [],
            'selectable' => false,
            'pagination' => null,
        ], $options);
    }

    public function tableClass(): string
    {
        $classes = ['table'];

        if ($this->options['striped']) {
            $classes[] = 'table-striped';
        }

        if ($this->options['bordered']) {
            $classes[] = 'table-bordered';
        }

        if ($this->options['hover']) {
            $classes[] = 'table-hover';
        }

        if ($this->options['small']) {
            $classes[] = 'table-sm';
        }

        return implode(' ', $classes);
    }

    public function wrapperClass(): string
    {
        $classes = [];

        if ($this->options['responsive']) {
            $classes[] = 'table-responsive';
        }

        return implode(' ', $classes);
    }

    public function hasData(): bool
    {
        return !empty($this->data) && (is_countable($this->data) ? count($this->data) > 0 : !empty($this->data));
    }

    public function getColumns(): array
    {
        if (!empty($this->columns)) {
            return $this->columns;
        }

        // If no columns defined and we have data, try to infer from first item
        if ($this->hasData()) {
            $firstItem = $this->getFirstItem();
            if (is_array($firstItem) || is_object($firstItem)) {
                return array_keys((array) $firstItem);
            }
        }

        return [];
    }

    public function getFirstItem()
    {
        if ($this->data instanceof Collection) {
            return $this->data->first();
        }

        if (is_array($this->data) && !empty($this->data)) {
            return reset($this->data);
        }

        return null;
    }

    public function getValue($item, $column)
    {
        if (is_array($item)) {
            return $item[$column] ?? null;
        }

        if (is_object($item)) {
            if (method_exists($item, $column)) {
                return $item->$column();
            }

            if (property_exists($item, $column)) {
                return $item->$column;
            }

            if (method_exists($item, 'getAttribute')) {
                return $item->getAttribute($column);
            }
        }

        return null;
    }

    public function isColumnActionable($column): bool
    {
        return isset($this->options['actions'][$column]) || in_array($column, $this->options['rowActions'] ?? []);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.table-component');
    }
}
