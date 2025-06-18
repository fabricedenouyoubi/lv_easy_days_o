<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3">
        <h6 class="fw-bold mb-0 d-flex align-items-center">
            <i class="fas fa-filter me-2 text-primary"></i>
            {{ $title }}
        </h6>
    </div>
    <div class="card-body pt-2 pb-3">
        <form wire:submit.prevent="{{ $filterAction }}">
            {{ $slot }}
            
            {{-- Boutons d'action par défaut --}}
            <div class="d-flex gap-2 mt-3">
                <x-action-button 
                    type="primary"
                    size="sm"
                    icon="fas fa-search"
                    text="Filtrer"
                    :wire-click="$filterAction"
                    :loading="true"
                    :loading-target="$filterAction" />
                
                <x-action-button 
                    type="outline-secondary"
                    size="sm"
                    icon="fas fa-refresh"
                    text="Réinitialiser"
                    :wire-click="$resetAction"
                    :loading="true"
                    :loading-target="$resetAction" />
            </div>
        </form>
    </div>
</div>