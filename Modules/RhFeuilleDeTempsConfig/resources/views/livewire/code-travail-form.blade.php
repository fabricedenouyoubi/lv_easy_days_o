{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    <form wire:submit.prevent="save">
        {{-- Code --}}
        <div class="mb-4">
            <label for="code" class="form-label">
                Code <span class="text-danger">*</span>
            </label>
            <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" wire:model="code"
                placeholder="Ex: VAC, CSN, CAISS..." style="text-transform: uppercase;">
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Le code sera automatiquement converti en majuscules
            </div>
        </div>

        {{-- Libellé --}}
        <div class="mb-4">
            <label for="libelle" class="form-label">
                Libellé <span class="text-danger">*</span>
            </label>
            <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                wire:model="libelle" placeholder="Ex: Vacances, Heure CSN, Banque de temps...">
            @error('libelle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Catégorie d'appartenance --}}
        <div class="mb-4">
            <label for="categorie_id" class="form-label">
                Catégorie d'appartenance <span class="text-danger">*</span>
            </label>
            <select id="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror"
                wire:model="categorie_id">
                <option value="">-- Sélectionner une catégorie --</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}">
                        {{ $categorie->intitule }}
                        @if ($categorie->configurable)
                            ({{ $categorie->valeur_config }})
                        @endif
                    </option>
                @endforeach
            </select>
            @error('categorie_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Choisissez la catégorie à laquelle appartient ce code de travail
            </div>
        </div>

        {{-- Information sur les catégories configurables --}}
        <div class="alert alert-light border mb-4">
            <h6 class="mb-2">
                <i class="fas fa-lightbulb me-2"></i>Information
            </h6>
            <p class="mb-0 small">
                Si vous sélectionnez une catégorie <strong>configurable</strong>, vous pourrez ensuite
                accéder aux options de configuration avancées pour ce code de travail.
            </p>
        </div>

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2">
            <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel'
                text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md" text="{{ $codeTravailId ? 'Modifier' : 'Créer' }}" typeButton='submit' />
        </div>
    </form>

    {{-- CSS local --}}
    <style>
        /* Style pour les codes */
        code {
            font-size: 0.9em;
            font-weight: bold;
        }

        /* Animation pour les badges */
        .badge {
            transition: all 0.3s ease;
        }

        /* Style pour l'aperçu */
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }

        /* Amélioration visuelle des labels requis */
        .text-danger {
            font-weight: bold;
        }

        /* Style pour les inputs en majuscules */
        #code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
    </style>
</div>
