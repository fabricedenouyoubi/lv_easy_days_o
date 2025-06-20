{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal avec tableau à gauche et filtres à droite --}}
    <div class="row">
        {{-- Colonne principale - Tableau des configurations --}}
        <div class="col-lg-8">

            <x-table-card title="{{ $titleModal }}" buttonIcon="fas fa-arrow-left me-2" icon="fas fa-users me-2"
                button-text="Retour" link="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employé</th>
                                <th>Quota (H)</th>
                                <th>Heures restant</th>
                                <th>Heures pris</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configurations as $configuration)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $configuration->employe->nom }}
                                                {{ $configuration->employe->prenom }}</strong>
                                            <br>
                                            <small
                                                class="text-muted">{{ $configuration->employe->matricule ?? 'Pas de matricule' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($configuration->quota > 0)
                                            <span
                                                class="badge bg-primary">{{ number_format($configuration->quota, 2) }}h</span>
                                        @else
                                            <span
                                                class="badge bg-secondary">{{ number_format($configuration->quota, 2) }}h</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-success">{{ number_format($configuration->reste, 2) }}h</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-warning">{{ number_format($configuration->consomme, 2) }}h</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Bouton Modifier --}}
                                            <x-action-button type="outline-primary" icon="fas fa-edit" tooltip="Modifier" wireClick="showEditModal({{ $configuration->id }})" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun employé trouvé avec ces critères</p>
                                        <small class="text-muted">
                                            Modifiez vos filtres de recherche
                                        </small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($configurations->hasPages())
                    <div class="mt-3">
                        {{ $configurations->links() }}
                    </div>
                @endif
            </x-table-card>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">

            <x-filter-card filterAction="filter">
                {{-- Filtre par employé --}}
                <div class="mb-3">
                    <label for="searchEmploye" class="form-label">Employé</label>
                    <input type="text" id="searchEmploye" class="form-control"
                        placeholder="Rechercher par nom ou prénom..." wire:model.defer="searchEmploye">
                </div>
            </x-filter-card>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>
                            Modifier le quota de
                            {{ $configurations->find($editingId)?->employe?->nom ?? 'l\'employé' }}
                        </h5>
                        <x-action-button type="close btn-close-primary" wireClick='closeModal' />
                    </div>
                    <div class="modal-body">
                        @if (!$anneeBudgetaireActive)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Aucune année financière active. Impossible d'ajouter une
                                configuration.
                            </div>
                        @else
                            <livewire:rh-comportement::individuel-form :configurationId="$editingId" :codeTravailId="$codeTravailId"
                                :key="$editingId" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
