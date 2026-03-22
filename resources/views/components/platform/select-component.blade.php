<div class="mb-3">
    @if($hasLabel())
        <label for="{{ $selectId() }}" class="{{ $labelClass() }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select {{ $attributes->merge($selectAttributes()) }}>
        @if($hasPlaceholder())
            <option value="" @if(!$isSelected('')) selected @endif>
                {{ $placeholder }}
            </option>
        @endif
        
        @foreach($getOptions() as $value => $label)
            <option value="{{ $value }}" @if($isSelected($value)) selected @endif>
                {{ $label }}
            </option>
        @endforeach
    </select>
    
    @if($hasHelp())
        <div class="form-text">{{ $help }}</div>
    @endif
    
    @if($hasError())
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>