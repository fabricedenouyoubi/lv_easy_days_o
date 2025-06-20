{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal avec tableau à gauche et filtres à droite --}}
    <div class="row">
        {{-- Colonne principale - Tableau des jours fériés --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-calendar-day me-2"></i>
                                Liste des {{ $titleModal }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <x-action-button type="outline-secondary" icon="fas fa-arrow-left"
                                href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}"
                                text="Retour" />
                            <x-action-button type="primary" icon="fas fa-plus me-2" wireClick="showCreateModal"
                                text="Nouveau" />
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Tableau des jours fériés --}}
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Jour</th>
                                    <th>Libellé</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($joursFeries as $jourFerie)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $jourFerie->formatted_date }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $jourFerie->jour_semaine }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $jourFerie->libelle }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Bouton Voir détails --}}
                                                <x-action-button type="outline-success" icon="fas fa-eye"
                                                    tooltip="Voir détails"
                                                    wireClick="showDetailModal({{ $jourFerie->id }})" />

                                                {{-- Bouton Modifier --}}
                                                <x-action-button type="outline-primary" icon="fas fa-edit"
                                                    tooltip="Modifier"
                                                    wireClick="showEditModal({{ $jourFerie->id }})" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucun(e) {{ $titleModal }} configuré</p>
                                            @if ($anneeBudgetaireActive)
                                                <small class="text-muted">
                                                    Cliquez sur "Nouveau" pour ajouter un(e) {{ $titleModal }}
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($joursFeries->hasPages())
                        <div class="mt-3">
                            {{ $joursFeries->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card filterAction="filter">
                {{-- Filtre par libellé du code --}}
                <div class="mb-3">
                    <label for="searchLibelle" class="form-label">Libellé du code</label>
                    <input type="text" id="searchLibelle" class="form-control"
                        placeholder="Rechercher par libellé..." wire:model.defer="searchLibelle">
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
                            <i class="fas fa-calendar-plus me-2"></i>
                            {{ $editingId ? 'Modifier ' . $titleModal : 'Créer ' . $titleModal }}
                        </h5>
                        <x-action-button type="close btn-close-primary"  wireClick="closeModal" />
                    </div>
                    <div class="modal-body">
                        @if (!$anneeBudgetaireActive)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Aucune année budgétaire active. Impossible d'ajouter un
                                jour férié.
                            </div>
                        @else
                            <livewire:rh-comportement::jour-ferie-form :jourFerieId="$editingId" :codeTravailId="$codeTravailId"
                                :key="$editingId" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Jour Férié --}}
    @if ($showDetail && $detailJourFerie)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails {{ $titleModal }}
                        </h5>
                        <x-action-button type="close btn-close-primary"  wireClick="closeDetailModal" />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar-day me-2"></i>Informations {{ $titleModal }}</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Libellé :</strong></td>
                                        <td>{{ $detailJourFerie->libelle }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date :</strong></td>
                                        <td>
                                            <strong>{{ $detailJourFerie->formatted_date }}</strong>
                                            <br><small class="text-muted">{{ $detailJourFerie->jour_semaine }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Année :</strong></td>
                                        <td>{{ $detailJourFerie->anneeBudgetaire->annee ?? 'Non définie' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-cog me-2"></i>Configuration</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Code travail :</strong></td>
                                        <td><code
                                                class="bg-light px-2 py-1 rounded">{{ $detailJourFerie->codeTravail->code }}</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type :</strong></td>
                                        <td><span class="badge bg-success">Jour férié global</span></td>
                                    </tr>
                                    <td><strong>Modifié le :</strong></td>
                                    <td>{{ $detailJourFerie->updated_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-action-button type="secondary"  wireClick="closeDetailModal" text="Fermer" icon="fas fa-times me-2" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
