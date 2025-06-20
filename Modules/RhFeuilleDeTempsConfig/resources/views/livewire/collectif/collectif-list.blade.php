{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal avec tableau à gauche et filtres à droite --}}
    <div class="row">
        {{-- Colonne principale - Tableau des configurations collectives --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-users-cog me-2"></i>
                                {{ $titleModal }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <x-action-button type="outline-secondary me-2" icon="fas fa-arrow-left me-2" text="Retour"
                                href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}" />
                            <x-action-button type="primary" icon="fas fa-plus me-2" text="Nouveau"
                                wireClick="showCreateModal" />
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Tableau des configurations collectives --}}
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th>Quota (H)</th>
                                    <th>Heures restant</th>
                                    <th>Heures pris</th>
                                    <th>Employés affectés</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($configurations as $configuration)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $configuration->libelle }}</strong>
                                                @if ($configuration->commentaire)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($configuration->commentaire, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-primary">{{ number_format($configuration->quota, 2) }}h</span>
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
                                            <span class="badge bg-info">
                                                <i
                                                    class="fas fa-users me-1"></i>{{ $configuration->nombre_employes_affectes }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Bouton Voir détails --}}
                                                <x-action-button type="outline-info" icon="fas fa-eye"
                                                    tooltip="Voir détails"
                                                    wireClick="showDetailModal({{ $configuration->id }})" />

                                                {{-- Bouton Modifier --}}

                                                <x-action-button type="outline-primary" icon="fas fa-edit"
                                                    tooltip="Modifier"
                                                    wireClick="showEditModal({{ $configuration->id }})" />

                                                {{-- Bouton Affecter --}}
                                                <x-action-button type="outline-success" icon="fas fa-user-plus"
                                                    tooltip="Affecter des employés"
                                                    wireClick="showAffectationModal({{ $configuration->id }})" />

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucune configuration collective trouvée</p>
                                            @if ($anneeBudgetaireActive)
                                                <small class="text-muted">
                                                    Cliquez sur "Nouveau" pour créer une configuration
                                                </small>
                                            @endif
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
                </div>
            </div>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card filterAction="filter">
                {{-- Filtre par libellé --}}
                <div class="mb-3">
                    <label for="searchLibelle" class="form-label">Libellé de la configuration</label>
                    <input type="text" id="searchLibelle" class="form-control"
                        placeholder="Rechercher par libellé..." wire:model.defer="searchLibelle">
                </div>
            </x-filter-card>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus me-2"></i>
                            {{ $editingId ? 'Modifier la configuration' : 'Nouvelle configuration' }}
                        </h5>
                        <x-action-button type="close btn-close-primary"wireClick='closeModal' />
                    </div>
                    <div class="modal-body">
                        @if (!$anneeBudgetaireActive)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Aucune année financière active. Impossible de créer une
                                configuration.
                            </div>
                        @else
                            <livewire:rh-comportement::collectif-form :configurationId="$editingId" :codeTravailId="$codeTravailId"
                                :key="$editingId" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Configuration --}}
    @if ($showDetail && $detailConfiguration)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de la configuration
                        </h5>
                        <x-action-button type="close btn-close-primary"wireClick='closeDetailModal' />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-cogs me-2"></i>Informations de la configuration</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Libellé :</strong></td>
                                        <td>{{ $detailConfiguration->libelle }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Quota total :</strong></td>
                                        <td><span
                                                class="badge bg-primary">{{ number_format($detailConfiguration->quota, 2) }}h</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Heures consommées :</strong></td>
                                        <td><span
                                                class="badge bg-warning">{{ number_format($detailConfiguration->consomme, 2) }}h</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Heures restantes :</strong></td>
                                        <td><span
                                                class="badge bg-success">{{ number_format($detailConfiguration->reste, 2) }}h</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-users me-2"></i>Employés affectés
                                    ({{ $detailConfiguration->employes->count() }})</h6>
                                @if ($detailConfiguration->employes->count() > 0)
                                    <div class="list-group">
                                        @foreach ($detailConfiguration->employes as $employe)
                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $employe->nom }} {{ $employe->prenom }}</strong>
                                                    @if ($employe->matricule)
                                                        <br><small class="text-muted">{{ $employe->matricule }}</small>
                                                    @endif
                                                </div>
                                                <span class="badge bg-secondary">
                                                    {{ number_format($employe->pivot->consomme_individuel, 2) }}h
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Aucun employé affecté à cette configuration
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-action-button type="secondary" wireClick='closeDetailModal' icon="fas fa-times me-2"
                            text="Fermer" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Affectation Employés --}}
    @if ($showAffectation)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>
                            Affecter des employés
                        </h5>
                        <x-action-button type="close btn-close-primary" wireClick='closeAffectationModal' />
                    </div>
                    <div class="modal-body">
                        <livewire:rh-comportement::affectation-employes :configurationId="$affectationConfigurationId" :key="$affectationConfigurationId" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
