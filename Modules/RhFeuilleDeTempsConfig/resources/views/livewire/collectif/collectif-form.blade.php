{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    <form wire:submit.prevent="save">
        {{-- Message d'information obligatoire --}}
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <h6 class="mb-1">Champs obligatoires (*)</h6>
                </div>
            </div>
        </div>

        {{-- INFORMATIONS DE BASE --}}
        <h6 class="mb-3">
            <i class="fas fa-cogs me-2"></i>
            INFORMATIONS DE LA CONFIGURATION
        </h6>

        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="libelle" class="form-label">
                        Libellé <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                        wire:model="libelle" placeholder="Ex: Formation équipe marketing">
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Nom de la configuration</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="quota" class="form-label">
                        Nombre d'heures total <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" id="quota" class="form-control @error('quota') is-invalid @enderror"
                            wire:model="quota" step="0.01" min="0" max="9999.99" placeholder="Ex: 40.00">
                        <span class="input-group-text">heures</span>
                    </div>
                    @error('quota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Quota total partagé
                    </small>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2 mt-4">

            <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel'
                text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md"
                text="{{ $configurationId ? 'Modifier' : 'Créer' }}" typeButton='submit'
                disabled="{{ !$anneeBudgetaireActive ? true : false }}" />
        </div>
    </form>
</div>
