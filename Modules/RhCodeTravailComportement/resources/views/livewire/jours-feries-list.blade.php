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
        {{-- Colonne principale - Tableau des jours fériés --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-calendar-day me-2"></i>
                                Liste des jours fériés
                            </h4>
                            <small class="text-muted">
                                Code: <code class="bg-light px-2 py-1 rounded">{{ $codeTravail->code }}</code> - 
                                {{ $codeTravail->libelle }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}" 
                               class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="button" class="btn btn-primary" wire:click="showCreateModal">
                                <i class="fas fa-plus me-2"></i>Nouveau jour
                            </button>
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
                                                <button class="btn btn-sm btn-outline-success" 
                                                        wire:click="showDetailModal({{ $jourFerie->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Bouton Modifier --}}
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        wire:click="showEditModal({{ $jourFerie->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucun jour férié configuré</p>
                                            @if($anneeBudgetaireActive)
                                                <small class="text-muted">
                                                    Cliquez sur "Nouveau jour" pour ajouter un jour férié
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($joursFeries->hasPages())
                        <div class="mt-3">
                            {{ $joursFeries->links() }}
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
                    {{-- Filtre par libellé du code --}}
                    <div class="mb-3">
                        <label for="searchLibelle" class="form-label">Libellé du code</label>
                        <input type="text" 
                               id="searchLibelle"
                               class="form-control" 
                               placeholder="Rechercher par libellé..." 
                               wire:model.live.debounce.300ms="searchLibelle">
                    </div>

                    {{-- Informations sur les résultats --}}
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $joursFeries->total() }} jour(s) férié(s) trouvé(s)
                        </small>
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
                                <td><strong>Catégorie :</strong></td>
                                <td><span class="badge bg-info">{{ $codeTravail->categorie->intitule }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Type :</strong></td>
                                <td><span class="badge bg-primary">{{ $codeTravail->categorie->valeur_config }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Formulaire Amélioré --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-plus me-2"></i>
                            {{ $editingId ? 'Modifier le jour férié' : 'Créer un jour férié' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-primary" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if(!$anneeBudgetaireActive)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Aucune année budgétaire active. Impossible d'ajouter un jour férié.
                            </div>
                        @else
                            <livewire:rh-comportement::jour-ferie-form 
                                :jourFerieId="$editingId" 
                                :codeTravailId="$codeTravailId" 
                                :key="$editingId" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Jour Férié --}}
    @if($showDetail && $detailJourFerie)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails du jour férié
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar-day me-2"></i>Informations du jour férié</h6>
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
                                        <td><code class="bg-light px-2 py-1 rounded">{{ $detailJourFerie->codeTravail->code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type :</strong></td>
                                        <td><span class="badge bg-success">Jour férié global</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Créé le :</strong></td>
                                        <td>{{ $detailJourFerie->created_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Modifié le :</strong></td>
                                        <td>{{ $detailJourFerie->updated_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">
                            <i class="fas fa-times me-2"></i>Fermer
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="showEditModal({{ $detailJourFerie->id }})">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>