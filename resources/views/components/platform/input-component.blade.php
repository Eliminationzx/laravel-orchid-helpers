<div class="mb-3">
    @if($hasLabel())
        <label for="{{ $inputId() }}" class="{{ $labelClass() }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input {{ $attributes->merge($inputAttributes()) }}>
    
    @if($hasHelp())
        <div class="form-text">{{ $help }}</div>
    @endif
    
    @if($hasError())
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>