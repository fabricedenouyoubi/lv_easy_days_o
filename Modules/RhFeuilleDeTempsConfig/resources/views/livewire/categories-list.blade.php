{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Layout principal avec tableau à gauche et filtres à droite --}}
    <div class="row">
        {{-- Colonne principale - Tableau des catégories --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-tags me-2"></i>
                                Liste des Catégories
                            </h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" wire:click="showCreateModal">
                                <i class="fas fa-plus me-2"></i>
                                Nouvelle Catégorie
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Tableau des catégories --}}
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Intitulé</th>
                                    <th>Configurable</th>
                                    <th>Valeur Config</th>
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
                                            @if($categorie->configurable)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Oui
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Non
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($categorie->configurable && $categorie->valeur_config)
                                                <span class="badge bg-info">{{ $categorie->valeur_config }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Bouton Détails --}}
                                                <button class="btn btn-sm btn-outline-info" 
                                                        wire:click="showDetailModal({{ $categorie->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Bouton Modifier --}}
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        wire:click="showEditModal({{ $categorie->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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
                </div>
            </div>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtres
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Filtre par intitulé --}}
                    <div class="mb-3">
                        <label for="search" class="form-label">Intitulé</label>
                        <input type="text" 
                               id="search"
                               class="form-control" 
                               placeholder="Rechercher par intitulé..." 
                               wire:model.defer="search">
                    </div>

                    {{-- Filtre par configuration --}}
                    <div class="mb-3">
                        <label for="filterConfigurable" class="form-label">Type de configuration</label>
                        <select id="filterConfigurable" 
                                class="form-select" 
                                wire:model.defer="filterConfigurable">
                            @foreach($filterOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-primary" 
                                wire:click="filter"
                                wire:loading.attr="disabled"
                                wire:target="filter">
                            <span wire:loading.remove wire:target="filter">
                                <i class="fas fa-search me-2"></i>Filtrer
                            </span>
                            <span wire:loading wire:target="filter">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Filtrage...
                            </span>
                        </button>

                        <button type="button" 
                                class="btn btn-outline-secondary" 
                                wire:click="resetFilters"
                                wire:loading.attr="disabled"
                                wire:target="resetFilters">
                            <span wire:loading.remove wire:target="resetFilters">
                                <i class="fas fa-refresh me-2"></i>Réinitialiser
                            </span>
                            <span wire:loading wire:target="resetFilters">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Réinitialisation...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-tags me-2"></i>
                            {{ $editingId ? 'Modifier la Catégorie' : 'Nouvelle Catégorie' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh-config::categories-form :categorieId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Catégorie --}}
    @if($showDetail && $detailCategorie)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de la Catégorie
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
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
                                            @if($detailCategorie->configurable)
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
                                            @if($detailCategorie->configurable && $detailCategorie->valeur_config)
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
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Fermer</button>
                        <button type="button" class="btn btn-primary" wire:click="showEditModal({{ $detailCategorie->id }})">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>