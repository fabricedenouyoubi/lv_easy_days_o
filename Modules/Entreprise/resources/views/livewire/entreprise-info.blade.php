<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <x-table-card title="Informations de l'Entreprise" icon="fas fa-building me-2" button-text="Modifier"
        buttonIcon="fas fa-edit me-2"
        button-action="{{ auth()->user()->can('Modifier les informations de l\'entreprise')  ? 'edit' : '' }}">

        <!-- Informations de l'entreprise -->
        <div>
            @if ($editing)
            <!-- Mode édition -->
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de l'entreprise <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="name"
                                class="form-control @error('name') is-invalid @enderror" wire:model="name"
                                placeholder="Nom de l'entreprise">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="premierJourSemaine" class="form-label">Premier jour de la semaine <span class="text-danger">*</span></label>
                            <select id="premierJourSemaine"
                                class="form-select @error('premierJourSemaine') is-invalid @enderror"
                                wire:model="premierJourSemaine">
                                @foreach($joursSemaineOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('premierJourSemaine')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description"
                                rows="4" placeholder="Description de l'entreprise"></textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel'
                        text="Annuler" />
                    <x-action-button type="success" icon="fas fa-save me-2" size="md" text="Enregistrer"
                        typeButton='submit' />
                </div>
            </form>
            @else
            <!-- Mode affichage -->
            @if ($entreprise)
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de l'entreprise</label>
                        <p class="form-control-plaintext">{{ $entreprise->name }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="form-control-plaintext">
                            {{ $entreprise->description ?: 'Aucune description renseignée' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Premier jour de la semaine</label>
                        <p class="form-control-plaintext">{{ $entreprise->premier_jour_semaine_libelle }}</p>
                    </div>
                </div>
            </div>

            @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5>Aucune entreprise configurée</h5>
                <p class="text-muted">Cliquez sur "Modifier" pour entrer les informations de l'entreprise.</p>
            </div>
            @endif
            @endif
        </div>
        </x-filter-card>
</div>