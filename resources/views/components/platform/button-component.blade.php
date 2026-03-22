<{{ $tagName() }}
    {{ $attributes->merge($attributes())->class($buttonClass()) }}
>
    @if($icon && $iconPosition === 'left')
        <x-orchid-icon :path="$icon" class="me-1" />
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <x-orchid-icon :path="$icon" class="ms-1" />
    @endif
</{{ $tagName() }}>