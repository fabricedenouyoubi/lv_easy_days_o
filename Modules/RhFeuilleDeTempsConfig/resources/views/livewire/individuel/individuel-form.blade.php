{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    <form wire:submit.prevent="save">
        {{-- Message d'information obligatoire --}}
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <h6 class="mb-1">Veuillez remplir tous les champs obligatoires marqués d'un astérisque (*)</h6>
                    <small class="text-muted">
                        La configuration sera appliquée pour l'année financière active.
                    </small>
                </div>
            </div>
        </div>

        {{-- INFORMATIONS EMPLOYÉ --}}
        <h6 class="mb-3">
            <i class="fas fa-user me-2"></i>
            INFORMATIONS EMPLOYÉ
        </h6>
        
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="employe_id" class="form-label">
                        Employé <span class="text-danger">*</span>
                    </label>
                    <select id="employe_id"
                            class="form-select @error('employe_id') is-invalid @enderror"
                            wire:model="employe_id"
                            @if($configurationId) disabled @endif>
                        <option value="">-- Sélectionner un employé --</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">
                                {{ $employe->nom }} {{ $employe->prenom }}
                                @if($employe->matricule)
                                    ({{ $employe->matricule }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('employe_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($configurationId)
                        <small class="form-text text-muted">L'employé ne peut pas être modifié une fois créé</small>
                    @else
                        <small class="form-text text-muted">Choisissez l'employé à configurer</small>
                    @endif
                </div>
            </div>
        </div>

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
                @if($employe_id && $quota)
                    @php
                        $selectedEmploye = $employes->find($employe_id);
                    @endphp
                    @if($selectedEmploye)
                        <div class="alert alert-success">
                            <h6 class="mb-2">
                                <i class="fas fa-eye me-2"></i>Aperçu de la configuration
                            </h6>
                            <p class="mb-1">
                                <strong>Employé :</strong> {{ $selectedEmploye->nom }} {{ $selectedEmploye->prenom }}
                            </p>
                            <p class="mb-1">
                                <strong>Quota :</strong> {{ number_format($quota, 2) }} heures
                            </p>
                            <p class="mb-0">
                                <strong>Reste initial :</strong> {{ number_format($quota, 2) }} heures
                            </p>
                        </div>
                    @endif
                @else
                    <div class="alert alert-light">
                        <i class="fas fa-info-circle me-2"></i>
                        Sélectionnez un employé et saisissez le quota pour voir l'aperçu
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
