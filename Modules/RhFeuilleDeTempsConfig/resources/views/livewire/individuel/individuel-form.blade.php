{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    <form wire:submit.prevent="save">
        {{-- Message d'information obligatoire --}}
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <h6 class="mb-1">Modification du quota de {{ $configuration?->employe?->nom }} {{ $configuration?->employe?->prenom }}</h6>
                    <small class="text-muted">
                        Modifiez le nombre d'heures alloué à cet employé.
                    </small>
                </div>
            </div>
        </div>

        {{-- INFORMATIONS EMPLOYÉ (lecture seule) --}}
        @if($configuration)
            <h6 class="mb-3">
                <i class="fas fa-user me-2"></i>
                INFORMATIONS EMPLOYÉ
            </h6>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-light">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $configuration->employe->nom }} {{ $configuration->employe->prenom }}</h6>
                                @if($configuration->employe->matricule)
                                    <small class="text-muted">Matricule : {{ $configuration->employe->matricule }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- INFORMATIONS TEMPORELLES --}}
        <h6 class="mb-3 mt-4">
            <i class="fas fa-clock me-2"></i>
            INFORMATIONS TEMPORELLES
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quota" class="form-label">
                        Nombre d'heures <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               id="quota"
                               class="form-control @error('quota') is-invalid @enderror" 
                               wire:model="quota"
                               step="0.01"
                               min="0"
                               max="9999.99"
                               placeholder="Ex: 24.00">
                        <span class="input-group-text">heures</span>
                    </div>
                    @error('quota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Quota d'heures alloué à cet employé pour l'année
                    </small>
                </div>
            </div>

            {{-- Aperçu des informations --}}
            <div class="col-md-6">
                @if($configuration && $quota)
                    <div class="alert alert-success">
                        <h6 class="mb-2">
                            <i class="fas fa-eye me-2"></i>Aperçu de la modification
                        </h6>
                        <p class="mb-1">
                            <strong>Quota actuel :</strong> {{ number_format($configuration->quota, 2) }} heures
                        </p>
                        <p class="mb-1">
                            <strong>Nouveau quota :</strong> {{ number_format($quota, 2) }} heures
                        </p>
                        <p class="mb-0">
                            <strong>Différence :</strong> 
                            @php $difference = $quota - $configuration->quota; @endphp
                            <span class="{{ $difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference, 2) }} heures
                            </span>
                        </p>
                    </div>
                @else
                    <div class="alert alert-light">
                        <i class="fas fa-info-circle me-2"></i>
                        Saisissez le nouveau quota pour voir l'aperçu
                    </div>
                @endif
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" wire:click="cancel">
                Annuler
            </button>
            <button type="submit" 
                    class="btn btn-primary"
                    @if(!$anneeBudgetaireActive) disabled @endif>
                <i class="fas fa-save me-2"></i>
                Modifier
            </button>
        </div>
    </form>
</div>