<form 
    {{ $attributes->merge($formAttributes())->class($formClass()) }}
>
    @if($isSpoofedMethod())
        @method($formMethod())
    @endif
    
    @csrf
    
    <div class="{{ $fieldClass() }}">
        {{ $slot }}
    </div>
    
    @if($submitText || $showCancel)
        <div class="{{ $buttonGroupClass() }}">
            @if($submitText)
                <button type="submit" class="btn btn-{{ $submitVariant }}">
                    {{ $submitText }}
                </button>
            @endif
            
            @if($showCancel && $cancelUrl)
                <a href="{{ $cancelUrl }}" class="btn btn-{{ $cancelVariant }}">
                    {{ $cancelText ?? __('Cancel') }}
                </a>
            @endif
        </div>
    @endif
</form>