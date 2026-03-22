<div class="mb-3">
    @if($hasLabel())
        <label for="{{ $textareaId() }}" class="{{ $labelClass() }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <textarea {{ $attributes->merge($textareaAttributes()) }}>{{ $value }}</textarea>
    
    @if($hasHelp())
        <div class="form-text">{{ $help }}</div>
    @endif
    
    @if($hasError())
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>