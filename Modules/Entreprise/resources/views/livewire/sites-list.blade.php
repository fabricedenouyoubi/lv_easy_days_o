<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <x-table-card title="Sites de l'Entreprise" icon="fas fa-map-marker-alt me-2" button-text="Nouveau Site"
        button-action="showCreateModal">

        <div>
            {{-- Barre de recherche avec bouton filtrer --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text"
                                class="form-control @if (isset($searchError) && $searchError) is-invalid @endif"
                                placeholder="Rechercher un site..." wire:model="search"
                                wire:keydown.enter="performSearch">

                            {{-- Bouton Filtrer --}}

                            <x-action-button type="outline-primary" icon="fas fa-search me-1" wireClick="performSearch" text="Filtrer" loadingTarget="performSearch"/>

                            {{-- <button type="button" class="btn btn-outline-primary" wire:click="performSearch"
                                wire:loading.attr="disabled" wire:target="performSearch">

                                <span wire:loading.remove wire:target="performSearch">
                                    <i class="fas fa-search me-1"></i>
                                    Filtrer
                                </span>
                                <span wire:loading wire:target="performSearch">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Recherche...
                                </span>
                            </button> --}}

                            {{-- Bouton Effacer - visible seulement si une recherche est active --}}
                            @if (isset($searchTerm) && $searchTerm)
                                <button type="button" class="btn btn-outline-secondary" wire:click="clearSearch"
                                    title="Effacer la recherche">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>

                        {{-- Message d'erreur de validation --}}
                        @if (isset($searchError) && $searchError)
                            <div class="invalid-feedback d-block mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $searchError }}
                            </div>
                        @endif

                        {{-- Indicateur de recherche active --}}
                        @if (isset($searchTerm) && $searchTerm)
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-filter me-1"></i>
                                Filtré par : "<strong>{{ $searchTerm }}</strong>"
                                <button type="button" class="btn btn-link btn-sm p-0 ms-1" wire:click="clearSearch">
                                    (effacer)
                                </button>
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Spinner de recherche --}}
            <div wire:loading wire:target="performSearch" class="position-relative">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                    style="background: rgba(255,255,255,0.8); z-index: 10;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Recherche en cours...</span>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Recherche en cours...</small>
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
                                        <x-action-button type="outline-info" icon="fas fa-eye" size="sm" tooltip="Voir détails" wireClick="showDetailModal({{ $site->id }})" />

                                        {{-- Bouton Modifier --}}
                                        <x-action-button type="outline-primary" icon="fas fa-edit" size="sm" tooltip="Modifier" wireClick="showEditModal({{ $site->id }})" />

                                        {{-- Bouton Supprimer --}}
                                        <!-- <button class="btn btn-sm btn-outline-danger"
                                                wire:click="confirmDelete({{ $site->id }})"
                                                data-bs-toggle="tooltip"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i> -->
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">
                                        @if (isset($searchTerm) && $searchTerm)
                                            Aucun site trouvé pour "{{ $searchTerm }}"
                                        @else
                                            Aucun site trouvé
                                        @endif
                                    </p>
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
                        <x-action-button type="secondary" wireClick="closeDetailModal" size="md" icon="fas fa-times" text="Fermer"/>
                        <x-action-button type="primary" wireClick="showEditModal({{ $detailSite->id }})" size="md" icon="fas fa-edit me-2" text="Modifier"/>
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
                        <x-action-button type="close" wireClick="cancelDelete"/>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce site ?</p>
                        <p class="text-muted">Cette action est irréversible et supprimera également l'adresse associée.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <x-action-button type="secondary" wireClick="cancelDelete" size="md" icon="fas fa-times" text="Annuler"/>
                        <x-action-button type="danger" wireClick="delete" size="md" icon="fas fa-trash me-2" text="Supprimer"/>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- CSS local --}}
    <style>
        .search-box .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .position-relative {
            min-height: 200px;
            margin-left: 500px;
        }
    </style>
</div>
