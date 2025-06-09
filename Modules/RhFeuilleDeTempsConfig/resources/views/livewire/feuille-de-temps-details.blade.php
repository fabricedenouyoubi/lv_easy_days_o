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

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="mdi mdi-filter me-2"></i>
                Filtres
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date début</label>
                    <input type="date" 
                           class="form-control" 
                           wire:model="dateDebut">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date fin</label>
                    <input type="date" 
                           class="form-control" 
                           wire:model="dateFin">
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="showOnlyActive" 
                               wire:model="showOnlyActive">
                        <label class="form-check-label" for="showOnlyActive">
                            Semaines actives seulement
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-primary flex-fill" 
                                wire:click="applyFilters"
                                wire:loading.attr="disabled"
                                wire:target="applyFilters">
                            <span wire:loading.remove wire:target="applyFilters">
                                <i class="mdi mdi-magnify me-1"></i>
                                Filtrer
                            </span>
                            <span wire:loading wire:target="applyFilters">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Filtrage...
                            </span>
                        </button>
                        <button type="button" 
                                class="btn btn-outline-secondary flex-fill" 
                                wire:click="resetFilters"
                                wire:loading.attr="disabled"
                                wire:target="resetFilters">
                            <span wire:loading.remove wire:target="resetFilters">
                                <i class="mdi mdi-refresh me-1"></i>
                                Réinitialiser
                            </span>
                            <span wire:loading wire:target="resetFilters">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Réinitialisation...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des feuilles de temps -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="mdi mdi-file-table me-2"></i>
                Semaines de l'années
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Semaine</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th class="text-center">Actif</th>
                            <th class="text-center">Paie</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feuilles as $feuille)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                <span class="fw-semibold text-primary">{{ $feuille->numero_semaine }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Semaine {{ $feuille->numero_semaine }}</h6>
                                            <p class="text-muted mb-0 small">{{ $feuille->periode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $feuille->debut->format('d/m/Y') }}</td>
                                <td>{{ $feuille->fin->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if($feuille->actif)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="mdi mdi-check-circle me-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="mdi mdi-close-circle me-1"></i>Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($feuille->est_semaine_de_paie)
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="mdi mdi-cash me-1"></i>Semaine de paie
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            <i class="mdi mdi-calendar me-1"></i>Normale
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-horizontal"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($feuille->actif)
                                                <li>
                                                    <button class="dropdown-item" 
                                                            wire:click="desactiverFeuille({{ $feuille->id }})">
                                                        <i class="mdi mdi-pause-circle text-warning me-2"></i>
                                                        Désactiver
                                                    </button>
                                                </li>
                                            @else
                                                <li>
                                                    <button class="dropdown-item" 
                                                            wire:click="activerFeuille({{ $feuille->id }})">
                                                        <i class="mdi mdi-play-circle text-success me-2"></i>
                                                        Activer
                                                    </button>
                                                </li>
                                            @endif
                                            
                                            <li>
                                                <button class="dropdown-item" 
                                                        wire:click="toggleSemaineDePaie({{ $feuille->id }})">
                                                    @if($feuille->est_semaine_de_paie)
                                                        <i class="mdi mdi-cash-remove text-secondary me-2"></i>
                                                        Retirer semaine de paie
                                                    @else
                                                        <i class="mdi mdi-cash-plus text-warning me-2"></i>
                                                        Marquer semaine de paie
                                                    @endif
                                                </button>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="mdi mdi-file-table text-muted mb-3" style="font-size: 48px;"></i>
                                        <h6 class="text-muted">Aucune semaine trouvée</h6>
                                        @if($appliedDateDebut || $appliedDateFin || $appliedShowOnlyActive)
                                            <p class="text-muted small mb-3">Essayez de modifier vos filtres</p>
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    wire:click="resetFilters">
                                                Réinitialiser les filtres
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($feuilles->hasPages())
            <div class="card-footer">
                {{ $feuilles->links() }}
            </div>
        @endif
    </div>

    <!-- Statistiques rapides -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="mdi mdi-file-table" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $feuilles->total() }}</h4>
                    <p class="text-muted mb-0 small">Total semaines</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="mdi mdi-check-circle" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $feuilles->where('actif', true)->count() }}</h4>
                    <p class="text-muted mb-0 small">Semaines actives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-close-circle" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $feuilles->where('actif', false)->count() }}</h4>
                    <p class="text-muted mb-0 small">Semaines non actives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="mdi mdi-cash" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $feuilles->where('est_semaine_de_paie', true)->count() }}</h4>
                    <p class="text-muted mb-0 small">Semaines de paie</p>
                </div>
            </div>
        </div>
    </div>
</div>