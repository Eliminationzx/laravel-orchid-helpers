<div class="{{ $wrapperClass() }}">
    <input {{ $attributes->merge($inputAttributes()) }}>
    
    @if($hasLabel())
        <label class="{{ $labelClass() }}" for="{{ $checkboxId() }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @if($hasHelp())
        <div class="form-text">{{ $help }}</div>
    @endif
    
    @if($hasError())
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>