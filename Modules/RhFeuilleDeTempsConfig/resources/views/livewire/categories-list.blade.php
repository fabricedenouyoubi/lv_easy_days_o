{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal avec tableau à gauche et filtres à droite --}}
    <div class="row">
        {{-- Colonne principale - Tableau des catégories --}}
        <div class="col-lg-8">
            <x-table-card title="Liste des Catégories" icon="fas fa-tags me-2" button-text="Nouvelle Catégorie"
                button-action="showCreateModal">
                {{-- Tableau des catégories --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Intitulé catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $categorie)
                                <tr>
                                    <td>
                                        <strong>{{ $categorie->intitule }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Bouton Détails --}}
                                            <x-action-button type="outline-info" icon="fas fa-eye"
                                                tooltip="Voir détails"
                                                wireClick="showDetailModal({{ $categorie->id }})" />

                                            {{-- Bouton Modifier --}}
                                            <x-action-button type="outline-primary" icon="fas fa-edit"
                                                tooltip="Modifier" wireClick="showEditModal({{ $categorie->id }})" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune catégorie trouvée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $categories->links() }}
                </div>
            </x-table-card>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">

            <x-filter-card filterAction="filter">
                {{-- Filtre par intitulé --}}
                <div class="mb-3">
                    <label for="search" class="form-label">Intitulé</label>
                    <input type="text" id="search" class="form-control" placeholder="Rechercher par intitulé..."
                        wire:model.defer="search">
                </div>

                {{-- Filtre par configuration --}}
                <div class="mb-3">
                    <label for="filterConfigurable" class="form-label">Type de configuration</label>
                    <select id="filterConfigurable" class="form-select" wire:model.defer="filterConfigurable">
                        @foreach ($filterOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </x-filter-card>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-tags me-2"></i>
                            {{ $editingId ? 'Modifier la Catégorie' : 'Nouvelle Catégorie' }}
                        </h5>
                        <x-action-button type="close" wireClick="closeModal" />
                    </div>
                    <div class="modal-body">
                        <livewire:rh-config::categories-form :categorieId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Catégorie --}}
    @if ($showDetail && $detailCategorie)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de la Catégorie
                        </h5>
                        <x-action-button type="close" wireClick="closeDetailModal" />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><i class="fas fa-tag me-2"></i>Informations Générales</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Intitulé :</strong></td>
                                        <td>{{ $detailCategorie->intitule }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Configurable :</strong></td>
                                        <td>
                                            @if ($detailCategorie->configurable)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Oui
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Non
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valeur config :</strong></td>
                                        <td>
                                            @if ($detailCategorie->configurable && $detailCategorie->valeur_config)
                                                <span class="badge bg-info">{{ $detailCategorie->valeur_config }}</span>
                                            @else
                                                <span class="text-muted">Non applicable</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='closeDetailModal'
                            text="Fermer" />
                        <x-action-button type="success" icon="fas fa-edit me-2" size="md" text="Modifier" wireClick="showEditModal({{ $detailCategorie->id }})" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
