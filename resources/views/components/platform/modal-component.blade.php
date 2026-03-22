<div 
    class="{{ $modalClass() }}" 
    id="{{ $id }}" 
    tabindex="-1" 
    aria-labelledby="{{ $id }}Label" 
    aria-hidden="true"
    @foreach($dataAttributes() as $key => $value)
        {{ $key }}="{{ $value }}"
    @endforeach
>
    <div class="{{ $dialogClass() }}">
        <div class="modal-content">
            @if($title || $showCloseButton)
                <div class="modal-header">
                    @if($title)
                        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    @endif
                    @if($showCloseButton)
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endif
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if($showCloseButton || $showSubmitButton)
                <div class="modal-footer">
                    @if($showCloseButton && $closeButtonText)
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $closeButtonText }}
                        </button>
                    @endif
                    
                    @if($showSubmitButton)
                        <button 
                            type="button" 
                            class="btn btn-{{ $submitButtonVariant }}"
                            @if($submitAction) onclick="{{ $submitAction }}" @endif
                        >
                            {{ $submitButtonText ?? __('Submit') }}
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>