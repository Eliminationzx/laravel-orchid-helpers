<div class="{{ $cardClass() }}">
    @if($hasHeader())
        <div class="card-header">
            <div class="d-flex align-items-center">
                @if($icon)
                    <x-orchid-icon :path="$icon" class="me-2" />
                @endif
                <div>
                    @if($title)
                        <h5 class="card-title mb-0">{{ $title }}</h5>
                    @endif
                    @if($subtitle)
                        <h6 class="card-subtitle mb-0 text-muted">{{ $subtitle }}</h6>
                    @endif
                </div>
            </div>
            @if($header)
                <div class="mt-2">
                    {{ $header }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $bodyClass() }}">
        {{ $slot }}
    </div>
    
    @if($hasFooter())
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>