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
                        Cette configuration pourra être partagée entre plusieurs employés.
                    </small>
                </div>
            </div>
        </div>

        {{-- INFORMATIONS DE BASE --}}
        <h6 class="mb-3">
            <i class="fas fa-cogs me-2"></i>
            INFORMATIONS DE LA CONFIGURATION
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="libelle" class="form-label">
                        Libellé <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="libelle"
                           class="form-control @error('libelle') is-invalid @enderror" 
                           wire:model="libelle"
                           placeholder="Ex: Formation équipe marketing">
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Nom de cette configuration collective</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quota" class="form-label">
                        Nombre d'heures total <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               id="quota"
                               class="form-control @error('quota') is-invalid @enderror" 
                               wire:model="quota"
                               step="0.01"
                               min="0"
                               max="9999.99"
                               placeholder="Ex: 40.00">
                        <span class="input-group-text">heures</span>
                    </div>
                    @error('quota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Quota total partagé entre tous les employés affectés
                    </small>
                </div>
            </div>
        </div>

        {{-- Aperçu de la configuration --}}
        @if($libelle && $quota)
            <div class="alert alert-success mt-3">
                <h6 class="mb-2">
                    <i class="fas fa-eye me-2"></i>Aperçu de la configuration
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <strong>Configuration :</strong> {{ $libelle }}
                        </p>
                        <p class="mb-0">
                            <strong>Quota total :</strong> {{ number_format($quota, 2) }} heures
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">
                            <strong>Type :</strong> <span class="badge bg-info">Collectif</span>
                        </p>
                        <p class="mb-0">
                            <strong>Partage :</strong> Entre plusieurs employés
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Information sur les étapes suivantes --}}
        <div class="alert alert-light border-start border-primary border-3 mt-4">
            <h6 class="mb-2">
                <i class="fas fa-lightbulb me-2"></i>Étapes suivantes
            </h6>
            <ol class="mb-0 ps-3">
                <li>Créer cette configuration collective</li>
                <li>Utiliser le bouton <strong>"Affecter"</strong> pour sélectionner les employés</li>
                <li>Les employés affectés partageront le quota d'heures défini</li>
            </ol>
        </div>

        {{-- Informations automatiques (en lecture seule) --}}
        @if($anneeBudgetaireActive)
            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="mb-2">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations automatiques
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Heures consommées</small>
                        <strong>0.00 heures</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Heures restantes</small>
                        <strong>{{ number_format($quota ?? 0, 2) }} heures</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Année financière</small>
                        <strong>{{ $anneeBudgetaireActive->libelle }}</strong>
                    </div>
                </div>
            </div>
        @endif

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" wire:click="cancel">
                Annuler
            </button>
            <button type="submit" 
                    class="btn btn-primary"
                    @if(!$anneeBudgetaireActive) disabled @endif>
                <i class="fas fa-save me-2"></i>
                {{ $configurationId ? 'Modifier' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
