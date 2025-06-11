{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    <form wire:submit.prevent="save">
        {{-- Intitulé de la catégorie --}}
        <div class="mb-4">
            <label for="intitule" class="form-label">
                Intitulé de la catégorie <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   id="intitule"
                   class="form-control @error('intitule') is-invalid @enderror" 
                   wire:model="intitule"
                   placeholder="Saisir l'intitulé de la catégorie">
            @error('intitule')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Switch Configurable --}}
        <div class="mb-4">
            <div class="form-check form-switch">
                <input type="checkbox" 
                       id="configurable"
                       class="form-check-input @error('configurable') is-invalid @enderror" 
                       wire:model.live="configurable"
                       role="switch">
                <label class="form-check-label" for="configurable">
                    <strong>Est configurable</strong>
                    <small class="text-muted d-block">
                        Activez cette option pour permettre la configuration de valeurs spécifiques
                    </small>
                </label>
                @error('configurable')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Select Valeur Configuration --}}
        <div class="mb-4">
            <label for="valeur_config" class="form-label">
                Valeur de configuration
                @if($configurable)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <select id="valeur_config"
                    class="form-select @error('valeur_config') is-invalid @enderror"
                    wire:model="valeur_config"
                    @if(!$configurable) disabled @endif>
                <option value="">
                    @if($configurable)
                        -- Sélectionner une valeur --
                    @else
                        Non applicable (configurable désactivé)
                    @endif
                </option>
                @if($configurable)
                    @foreach($valeurConfigOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                @endif
            </select>
            @error('valeur_config')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            {{-- Aide contextuelle --}}
            <div class="form-text">
                @if($configurable)
                    <i class="fas fa-info-circle me-1"></i>
                    Choisissez le type de configuration : 
                    <strong>Individuel</strong> (pour personne), 
                    <strong>Collectif</strong> (pour groupes), 
                    <strong>Jour</strong> (pour jour fermé)
                @else
                    <i class="fas fa-lock me-1 text-muted"></i>
                    <span class="text-muted">
                        Activez "Est configurable" pour choisir une valeur de configuration
                    </span>
                @endif
            </div>
        </div>

        {{-- Aperçu de la configuration --}}
        @if($configurable && $valeur_config)
            <div class="alert alert-info mb-4">
                <h6 class="mb-2">
                    <i class="fas fa-eye me-2"></i>Aperçu de la configuration
                </h6>
                <p class="mb-0">
                    La catégorie <strong>"{{ $intitule ?: 'Sans nom' }}"</strong> 
                    sera configurée en mode <strong>{{ $valeur_config }}</strong>.
                </p>
            </div>
        @endif

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" wire:click="cancel">
                <i class="fas fa-times me-2"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>
                {{ $categorieId ? 'Modifier' : 'Créer' }}
            </button>
        </div>
    </form>

    {{-- CSS local --}}
    <style>
        /* Style pour le select désactivé */
        .form-select:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Animation pour le switch */
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Style pour l'aperçu */
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }

        /* Amélioration visuelle des labels requis */
        .text-danger {
            font-weight: bold;
        }
    </style>
</div>