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
        {{-- Colonne principale - Tableau des configurations --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>
                                {{ $titleModal }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}" 
                               class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="button" class="btn btn-primary" wire:click="showCreateModal">
                                <i class="fas fa-plus me-2"></i>Nouveau
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Tableau des configurations --}}
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Employé</th>
                                    <th>Nombre d'heures</th>
                                    <th>Nombre d'heures restant</th>
                                    <th>Nombre d'heures pris</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($configurations as $configuration)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $configuration->employe->nom }} {{ $configuration->employe->prenom }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $configuration->employe->matricule ?? 'Pas de matricule' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ number_format($configuration->quota, 2) }}h</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($configuration->reste, 2) }}h</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ number_format($configuration->consomme, 2) }}h</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Bouton Modifier --}}
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        wire:click="showEditModal({{ $configuration->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucune configuration d'employé trouvée</p>
                                            @if($anneeBudgetaireActive)
                                                <small class="text-muted">
                                                    Cliquez sur "Nouveau" pour configurer.
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($configurations->hasPages())
                        <div class="mt-3">
                            {{ $configurations->links() }}
                        </div>
                    @endif
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
                    {{-- Filtre par employé --}}
                    <div class="mb-3">
                        <label for="searchEmploye" class="form-label">Employé</label>
                        <input type="text" 
                               id="searchEmploye"
                               class="form-control" 
                               placeholder="Rechercher par nom ou prénom..." 
                               wire:model.defer="searchEmploye">
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

                    {{-- Information sur le code de travail --}}
                    <div class="mt-3 p-3 border rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-cog me-2"></i>Configuration actuelle
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td><strong>Code :</strong></td>
                                <td><code class="bg-light px-2 py-1 rounded">{{ $codeTravail->code }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Libellé :</strong></td>
                                <td>{{ $codeTravail->libelle }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type :</strong></td>
                                <td><span class="badge bg-info">{{ $codeTravail->categorie->valeur_config }}</span></td>
                            </tr>
                        </table>
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
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>
                            {{ $editingId ? 'Modifier la configuration' : $titleModal }}
                        </h5>
                        <button type="button" class="btn-close btn-close-primary" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if(!$anneeBudgetaireActive)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Aucune année financière active. Impossible d'ajouter une configuration.
                            </div>
                        @else
                            <livewire:rh-comportement::individuel-form 
                                :configurationId="$editingId" 
                                :codeTravailId="$codeTravailId" 
                                :key="$editingId" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>