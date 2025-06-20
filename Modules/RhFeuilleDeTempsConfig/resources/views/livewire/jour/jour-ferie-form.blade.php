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
            <i class="fas fa-clipboard-list me-2"></i>
            INFORMATIONS DE BASE
        </h6>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="libelle" class="form-label">
                        Libellé <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                        wire:model="libelle" placeholder="Ex: Jour de Noël, Fête du Canada...">
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="commentaire" class="form-label">Commentaire</label>
                    <textarea id="commentaire" class="form-control @error('commentaire') is-invalid @enderror" wire:model="commentaire"
                        rows="3" placeholder="Commentaire optionnel sur ce jour férié..."></textarea>
                    @error('commentaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- INFORMATIONS TEMPORELLES --}}
        <h6 class="mb-3 mt-4">
            <i class="fas fa-calendar me-2"></i>
            INFORMATIONS TEMPORELLES
        </h6>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="date" class="form-label">
                        Jour <span class="text-danger">*</span>
                    </label>
                    <input type="date" id="date" class="form-control @error('date') is-invalid @enderror"
                        wire:model="date"
                        @if ($anneeBudgetaireActive) min="{{ $anneeBudgetaireActive->debut->format('Y-m-d') }}"
                               max="{{ $anneeBudgetaireActive->fin->format('Y-m-d') }}" @endif>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        @if ($anneeBudgetaireActive)
                            Période autorisée : {{ $anneeBudgetaireActive->debut->format('d/m/Y') }}
                            à {{ $anneeBudgetaireActive->fin->format('d/m/Y') }}
                        @else
                            Aucune année budgétaire active
                        @endif
                    </small>
                </div>
            </div>

            {{-- Aperçu de la date sélectionnée --}}
            <div class="col-md-6">
                @if ($date)
                    @php
                        $dateCarbon = \Carbon\Carbon::parse($date);
                        $jours = [
                            'Monday' => 'Lundi',
                            'Tuesday' => 'Mardi',
                            'Wednesday' => 'Mercredi',
                            'Thursday' => 'Jeudi',
                            'Friday' => 'Vendredi',
                            'Saturday' => 'Samedi',
                            'Sunday' => 'Dimanche',
                        ];
                        $jourFr = $jours[$dateCarbon->format('l')] ?? $dateCarbon->format('l');
                    @endphp
                    <div class="alert alert-success">
                        <h6 class="mb-2">
                            <i class="fas fa-calendar-check me-2"></i>Aperçu de la date
                        </h6>
                        <p class="mb-1">
                            <strong>Date complète :</strong> {{ $jourFr }} {{ $dateCarbon->format('d M Y') }}
                        </p>
                        <p class="mb-0">
                            <strong>Libellé :</strong> {{ $libelle ?: 'À définir' }}
                        </p>
                    </div>
                @else
                    <div class="alert alert-light">
                        <i class="fas fa-calendar me-2"></i>
                        Sélectionnez une date pour voir l'aperçu
                    </div>
                @endif
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel'
                text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md"
                text="{{ $jourFerieId ? 'Modifier' : 'Créer' }}" typeButton='submit'
                disabled={{ !$anneeBudgetaireActive ? true : false }} />
        </div>
    </form>
</div>
