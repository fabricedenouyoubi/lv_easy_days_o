@if($show)
    <div class="modal fade show d-block" 
         tabindex="-1" 
         style="background-color: rgba(0,0,0,0.5);"
         wire:key="modal-{{ uniqid() }}">
        <div class="modal-dialog modal-{{ $size }}" role="document">
            <div class="modal-content">
                {{-- Header --}}
                @if($title)
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            @if($icon)
                                <i class="{{ $icon }} me-2"></i>
                            @endif
                            {{ $title }}
                        </h5>
                        <button type="button" 
                                class="btn-close" 
                                wire:click="{{ $closeAction }}"
                                aria-label="Close"></button>
                    </div>
                @endif

                {{-- Body --}}
                <div class="modal-body">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @if($showFooter && isset($footer))
                    <div class="modal-footer">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif