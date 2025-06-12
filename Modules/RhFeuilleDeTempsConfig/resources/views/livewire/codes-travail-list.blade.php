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
        {{-- Colonne principale - Tableau des codes de travail --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Liste des codes de travail
                            </h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" wire:click="showCreateModal">
                                <i class="fas fa-plus me-2"></i>
                                Nouveau Code
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Tableau des codes de travail --}}
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Libellé</th>
                                    <th>Catégorie</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($codesTravail as $codeTravail)
                                    <tr>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $codeTravail->code }}</code>
                                        </td>
                                        <td>
                                            <strong>{{ $codeTravail->libelle }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $codeTravail->categorie->intitule }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Bouton Voir détails --}}
                                                <button class="btn btn-sm btn-outline-info" 
                                                        wire:click="showDetailModal({{ $codeTravail->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Bouton Modifier --}}
                                                <button class="btn btn-sm btn-outline-success" 
                                                        wire:click="showEditModal({{ $codeTravail->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                {{-- Bouton Configuration (conditionnel) --}}
                                                @if($codeTravail->isConfigurable())
                                                    <a href="{{ route('rhfeuilledetempsconfig.codes-travail.configure', $codeTravail->id) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip" 
                                                       title="Configuration">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucun code de travail trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $codesTravail->links() }}
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
                    {{-- Filtre par code --}}
                    <div class="mb-3">
                        <label for="searchCode" class="form-label">Code</label>
                        <input type="text" 
                               id="searchCode"
                               class="form-control" 
                               placeholder="Rechercher par code..." 
                               wire:model.defer="searchCode">
                    </div>

                    {{-- Filtre par libellé du code --}}
                    <div class="mb-3">
                        <label for="searchLibelle" class="form-label">Libellé du code</label>
                        <input type="text" 
                               id="searchLibelle"
                               class="form-control" 
                               placeholder="Rechercher par libellé..." 
                               wire:model.defer="searchLibelle">
                    </div>

                    {{-- Filtre par catégorie --}}
                    <div class="mb-3">
                        <label for="filterCategorie" class="form-label">Catégorie</label>
                        <select id="filterCategorie" 
                                class="form-select" 
                                wire:model.defer="filterCategorie">
                            <option value="">-- Toutes --</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->intitule }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="d-flex gap-3">
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
                            <i class="fas fa-clipboard-list me-2"></i>
                            {{ $editingId ? 'Modifier le Code de travail' : 'Nouveau Code de travail' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh-config::code-travail-form :codeTravailId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Code de travail --}}
    @if($showDetail && $detailCodeTravail)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails du Code de travail
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-clipboard-list me-2"></i>Informations du Code</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Code :</strong></td>
                                        <td><code class="bg-light px-2 py-1 rounded">{{ $detailCodeTravail->code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Libellé :</strong></td>
                                        <td>{{ $detailCodeTravail->libelle }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Catégorie :</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ $detailCodeTravail->categorie->intitule }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Configurable :</strong></td>
                                        <td>
                                            @if($detailCodeTravail->isConfigurable())
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
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Fermer</button>
                        <button type="button" class="btn btn-success" wire:click="showEditModal({{ $detailCodeTravail->id }})">
                            <i class="fas fa-cog me-2"></i>Modifier
                        </button>
                        @if($detailCodeTravail->isConfigurable())
                            <a href="{{ route('rhfeuilledetempsconfig.codes-travail.configure', $detailCodeTravail->id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-sliders-h me-2"></i>Configuration
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
