<div>
    <!-- Messages de feedback -->
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

    <!-- En-tête avec recherche et bouton créer -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0">Gestion des Années Financières</h4>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" wire:click="showCreateModal">
                        <i class="mdi mdi-plus" class="fill-white me-2"></i>
                        Nouvelle Année
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Barre de recherche -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Rechercher par année..." 
                                   wire:model.live.debounce.300ms="search">
                            <i class="search-icon" class="mdi mdi-magnify"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des années financières -->
            <div class="table-responsive">
                <table class="table table-nowrap align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Période</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($annees as $annee)
                            <tr>
                                <td>
                                    <strong>{{ $annee->libelle }}</strong>
                                    @if($annee->statut === 'ACTIF')
                                        <span class="badge bg-success ms-2">Actuel</span>
                                    @endif
                                </td>
                                <td>{{ $annee->debut->format('d/m/Y') }}</td>
                                <td>{{ $annee->fin->format('d/m/Y') }}</td>
                                <td>
                                    @if($annee->statut === 'ACTIF')
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check me-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="mdi mdi-close me-1"></i>Inactif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- Bouton Modifier -->
                                        <button class="btn btn-sm btn-outline-primary" 
                                                wire:click="showEditModal({{ $annee->id }})"
                                                data-bs-toggle="tooltip" 
                                                title="Modifier">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        <!-- Bouton Activer (si pas active) -->
                                        @if($annee->statut !== 'ACTIF')
                                            <button class="btn btn-sm btn-outline-success" 
                                                    wire:click="activer({{ $annee->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="Activer">
                                                <i class="mdi mdi-check-circle"></i>
                                            </button>
                                        @endif

                                        <!-- Bouton Clôturer (si active) -->
                                        @if($annee->statut === 'ACTIF')
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    wire:click="cloturerEtCreerSuivante({{ $annee->id }})"
                                                    onclick="return confirm('Clôturer cette année et créer la suivante ?')"
                                                    data-bs-toggle="tooltip" 
                                                    title="Clôturer et créer suivante">
                                                <i class="mdi mdi-archive"></i>
                                            </button>
                                        @endif

                                        <!-- Bouton Supprimer (si pas active) -->
                                        @if($annee->statut !== 'ACTIF')
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    wire:click="confirmDelete({{ $annee->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="Supprimer">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-calendar-alt text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2 mb-0">Aucune année financière trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $annees->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Formulaire -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingId ? 'Modifier' : 'Créer' }} une Année Financière
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:budget::annee-financiere-form :anneeId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmation Suppression -->
    @if($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette année financière ?</p>
                        <p class="text-danger"><small>Cette action est irréversible.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialiser les tooltips
    document.addEventListener('livewire:navigated', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush