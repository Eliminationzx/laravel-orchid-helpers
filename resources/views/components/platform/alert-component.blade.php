<div class="alert {{ $alertClass() }} @if($dismissible) alert-dismissible fade show @endif" role="alert">
    <div class="d-flex align-items-center">
        @if($alertIcon())
            <x-orchid-icon :path="$alertIcon()" class="me-2" />
        @endif
        <div>
            {{ $message }}
        </div>
    </div>
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>