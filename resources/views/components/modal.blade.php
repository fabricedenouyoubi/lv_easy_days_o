@if($show)
    <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
    <div class="modal fade show" 
         tabindex="-1" 
         style="display: block; z-index: 1050;"
         wire:key="modal-{{ $title }}-{{ now()->timestamp }}">
        <div class="modal-dialog modal-{{ $size }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($icon)
                            <i class="{{ $icon }} me-2"></i>
                        @endif
                        {{ $title }}
                    </h5>
                    <x-action-button 
                        type="close"
                        wire-click="{{ $closeAction }}" />
                </div>
                <div class="modal-body">
                    {{ $slot }}
                </div>
                @if($showFooter)
                    <div class="modal-footer">
                        {{ $footer ?? '' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
>>>>>>> f5882d1e2b55b8ecf08c055036be74ca8ece889e
