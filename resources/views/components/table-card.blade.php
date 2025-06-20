<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="card-title mb-0 d-flex align-items-center">
                    @if ($icon)
                        <i class="{{ $icon }} me-2"></i>
                    @endif
                    {{ $title }}
                </h4>
            </div>
            @if (($canAdd && $buttonText && $buttonAction) || $link)
                @if ($link)
                    <div class="col-auto">
                        <x-action-button type="primary" size="sm" :icon="$buttonIcon" :text="$buttonText"
                            href="{{ $link }}" />
                    </div>
                @else
                    <div class="col-auto">
                        <x-action-button type="primary" size="sm" :icon="$buttonIcon" :text="$buttonText"
                            :wire-click="$buttonAction" />
                    </div>
                @endif
            @endif
        </div>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
