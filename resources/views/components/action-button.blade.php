@if($href)
    <a href="{{ $href }}"
       class="btn btn-{{ $size }} btn-{{ $type }}"
       @if($tooltip) data-bs-toggle="tooltip" title="{{ $tooltip }}" @endif>
        @if($icon)
            <i class="{{ $icon }} @if($text) me-2 @endif"></i>
        @endif
        @if($text)
            {{ $text }}
        @endif
    </a>
@else
    <button type="{{ $typeButton ?? 'button' }}"
            class="btn btn-{{ $size }} btn-{{ $type }}"
            @if($wireClick) wire:click="{{ $wireClick }}" @endif
            @if($tooltip) data-bs-toggle="tooltip" title="{{ $tooltip }}" @endif
            @if($disabled) disabled @endif
            @if($loading && $loadingTarget) wire:loading.attr="disabled" wire:target="{{ $loadingTarget }}" @endif>

        @if($loading && $loadingTarget)
            <span wire:loading.remove wire:target="{{ $loadingTarget }}">
                @if($icon)
                    <i class="{{ $icon }} @if($text) me-2 @endif"></i>
                @endif
                @if($text)
                    {{ $text }}
                @endif
            </span>
            <span wire:loading wire:target="{{ $loadingTarget }}">
                <span class="spinner-border spinner-border-sm @if($text) me-2 @endif" role="status"></span>
                @if($text)
                    Chargement...
                @endif
            </span>
        @else
            @if($icon)
                <i class="{{ $icon }} @if($text) me-2 @endif"></i>
            @endif
            @if($text)
                {{ $text }}
            @endif
        @endif
    </button>
@endif
