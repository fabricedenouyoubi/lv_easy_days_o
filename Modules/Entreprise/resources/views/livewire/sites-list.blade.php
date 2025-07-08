<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <x-table-card title="Sites de l'Entreprise" icon="fas fa-map-marker-alt me-2" button-text="Nouveau Site"
        button-action="{{ auth()->user()->can('Ajouter un nouveau site') ? 'showCreateModal' : '' }}">

        <div>
            {{-- Barre de recherche avec bouton filtrer --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                placeholder="Rechercher un site..."
                                wire:model="search"
                                wire:keydown.enter="performSearch">

                            <x-action-button type="outline-primary"
                                icon="fas fa-search me-1"
                                wireClick="performSearch"
                                text="Rechercher"
                                loadingTarget="performSearch" />

                            {{-- Bouton Effacer - visible seulement si une recherche est active --}}
                            @if($searchTerm)
                            <button type="button" class="btn btn-outline-secondary"
                                wire:click="clearSearch"
                                title="Effacer la recherche">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tableau des sites --}}
            <div class="table-responsive">
                <table class="table table-nowrap align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du Site</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sites as $site)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $site->name }}</strong>
                                    @if ($site->description)
                                    <br><small
                                        class="text-muted">{{ Str::limit($site->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($site->adresse)
                                <div>
                                    {{ $site->adresse->rue }}<br>
                                    <small class="text-muted">
                                        {{ $site->adresse->ville }}, {{ $site->adresse->code_postal }}
                                    </small>
                                </div>
                                @else
                                <span class="text-muted">Aucune adresse</span>
                                @endif
                            </td>
                            <td>
                                @if ($site->adresse && $site->adresse->telephone)
                                <div>
                                    <i class="fas fa-phone me-1"></i>{{ $site->adresse->telephone }}
                                    @if ($site->adresse->telephone_pro)
                                    <br><small class="text-muted">
                                        <i
                                            class="fas fa-briefcase me-1"></i>{{ $site->adresse->telephone_pro }}
                                        @if ($site->adresse->telephone_pro_ext)
                                        ({{ $site->adresse->telephone_pro_ext }})
                                        @endif
                                    </small>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">Aucun téléphone</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    {{-- Bouton Détails --}}
                                    @can('Voir les détails d\'un site')
                                    <x-action-button type="outline-info" icon="fas fa-eye" size="sm"
                                        tooltip="Voir détails" wireClick="showDetailModal({{ $site->id }})" />
                                    @endcan


                                    {{-- Bouton Modifier --}}
                                    @can('Modifier un site')
                                    <x-action-button type="outline-primary" icon="fas fa-edit" size="sm"
                                        tooltip="Modifier" wireClick="showEditModal({{ $site->id }})" />
                                    @endcan

                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">                                   
                                   Aucun site trouvé                                   
                                </p>
                                @if($searchTerm)
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                    wire:click="clearSearch">
                                    <i class="fas fa-refresh me-1"></i>
                                    Afficher tous les sites
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $sites->links() }}
            </div>
        </div>
    </x-table-card>




    {{-- Modal Formulaire --}}
    @if ($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $editingId ? 'Modifier le Site' : 'Nouveau Site' }}
                    </h5>
                    <x-action-button type="close" wireClick="closeModal" />
                </div>
                <div class="modal-body">
                    <livewire:entreprise::site-form :siteId="$editingId" :key="$editingId" />
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Détail Site --}}
    @if ($showDetail && $detailSite)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Détails du Site
                    </h5>
                    <x-action-button type="close" wireClick="closeDetailModal" />
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-building me-2"></i>Informations du Site</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $detailSite->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description :</strong></td>
                                    <td>{{ $detailSite->description ?: 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Entreprise :</strong></td>
                                    <td>{{ $detailSite->entreprise->name ?? 'Non définie' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Adresse</h6>
                            @if ($detailSite->adresse)
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Rue :</strong></td>
                                    <td>{{ $detailSite->adresse->rue }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ville :</strong></td>
                                    <td>{{ $detailSite->adresse->ville }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Code postal :</strong></td>
                                    <td>{{ $detailSite->adresse->code_postal }}</td>
                                </tr>
                                @if ($detailSite->adresse->appartement)
                                <tr>
                                    <td><strong>Appartement :</strong></td>
                                    <td>{{ $detailSite->adresse->appartement }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Téléphone :</strong></td>
                                    <td>{{ $detailSite->adresse->telephone }}</td>
                                </tr>
                                @if ($detailSite->adresse->telephone_pro)
                                <tr>
                                    <td><strong>Tél. Pro :</strong></td>
                                    <td>
                                        {{ $detailSite->adresse->telephone_pro }}
                                        @if ($detailSite->adresse->telephone_pro_ext)
                                        ({{ $detailSite->adresse->telephone_pro_ext }})
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                            @else
                            <p class="text-muted">Aucune adresse renseignée</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-action-button type="secondary" wireClick="closeDetailModal" size="md"
                        icon="fas fa-times" text="Fermer" />
                    <x-action-button type="primary" wireClick="showEditModal({{ $detailSite->id }})"
                        size="md" icon="fas fa-edit me-2" text="Modifier" />
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- Modal Confirmation Suppression --}}
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Confirmer la suppression
                    </h5>
                    <x-action-button type="close" wireClick="cancelDelete" />
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce site ?</p>
                    <p class="text-muted">Cette action est irréversible et supprimera également l'adresse associée.
                    </p>
                </div>
                <div class="modal-footer">
                    <x-action-button type="secondary" wireClick="cancelDelete" size="md"
                        icon="fas fa-times" text="Annuler" />
                    <x-action-button type="danger" wireClick="delete" size="md" icon="fas fa-trash me-2"
                        text="Supprimer" />
                </div>
            </div>
        </div>
    </div>
    @endif

</div>