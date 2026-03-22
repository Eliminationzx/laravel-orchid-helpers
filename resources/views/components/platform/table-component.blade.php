@if($wrapperClass())
    <div class="{{ $wrapperClass() }}">
@endif

<table class="{{ $tableClass() }}">
    @if($options['header'] && $getColumns())
        <thead>
            <tr>
                @if($options['selectable'])
                    <th scope="col" style="width: 40px;">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                @endif
                
                @foreach($getColumns() as $column)
                    <th scope="col">
                        @if(is_array($column))
                            {{ $column['label'] ?? $column['key'] ?? $column }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                        @endif
                    </th>
                @endforeach
                
                @if(!empty($options['rowActions']))
                    <th scope="col" style="width: 100px;">{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
    @endif
    
    <tbody>
        @if($hasData())
            @foreach($data as $index => $item)
                <tr>
                    @if($options['selectable'])
                        <td>
                            <input type="checkbox" class="form-check-input" name="selected[]" value="{{ $index }}">
                        </td>
                    @endif
                    
                    @foreach($getColumns() as $column)
                        @php
                            $columnKey = is_array($column) ? ($column['key'] ?? $column) : $column;
                            $value = $getValue($item, $columnKey);
                            $formattedValue = is_array($column) && isset($column['format']) ? $column['format']($value, $item) : $value;
                        @endphp
                        <td>
                            @if($isColumnActionable($columnKey) && isset($options['actions'][$columnKey]))
                                @php $action = $options['actions'][$columnKey]; @endphp
                                <a href="{{ is_callable($action) ? $action($item) : $action }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    {{ $formattedValue }}
                                </a>
                            @else
                                {!! $formattedValue !!}
                            @endif
                        </td>
                    @endforeach
                    
                    @if(!empty($options['rowActions']))
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @foreach($options['rowActions'] as $action)
                                    @if(is_array($action))
                                        <a href="{{ $action['url'] ?? '#' }}" 
                                           class="btn btn-{{ $action['variant'] ?? 'outline-primary' }}"
                                           title="{{ $action['title'] ?? '' }}">
                                            @if($action['icon'] ?? false)
                                                <x-orchid-icon :path="$action['icon']" />
                                            @else
                                                {{ $action['label'] ?? '' }}
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ count($getColumns()) + ($options['selectable'] ? 1 : 0) + (!empty($options['rowActions']) ? 1 : 0) }}" 
                    class="text-center text-muted py-4">
                    {{ $options['emptyText'] }}
                </td>
            </tr>
        @endif
    </tbody>
    
    @if($options['footer'] && $getColumns())
        <tfoot>
            <tr>
                @if($options['selectable'])
                    <th></th>
                @endif
                @foreach($getColumns() as $column)
                    <th>
                        @if(is_array($column))
                            {{ $column['label'] ?? $column['key'] ?? $column }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                        @endif
                    </th>
                @endforeach
                @if(!empty($options['rowActions']))
                    <th></th>
                @endif
            </tr>
        </tfoot>
    @endif
</table>

@if($wrapperClass())
    </div>
@endif

@if($options['pagination'])
    <div class="mt-3">
        {{ $options['pagination'] }}
    </div>
@endif