<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal --}}
    <div class="row">
        {{-- Colonne principale - Tableau --}}
        <div class="col-lg-8">
            <x-table-card title="Liste des codes de travail" icon="fas fa-clipboard-list" button-text="Nouveau Code"
                button-action="{{ auth()->user()->can('Ajouter un code de travail') ? 'showCreateModal' : '' }}">

                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Catégorie</th>
                                <th>Libellé</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($codesTravail as $codeTravail)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $codeTravail->categorie->intitule }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $codeTravail->libelle }}</strong>
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $codeTravail->code }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Boutons avec composant --}}
                                            <x-action-button type="outline-info" icon="fas fa-eye"
                                                tooltip="Voir détails"
                                                wire-click="showDetailModal({{ $codeTravail->id }})" />

                                            @can('Modifier un code de travail')
                                                <x-action-button type="outline-success" icon="fas fa-edit"
                                                    tooltip="Modifier" wire-click="showEditModal({{ $codeTravail->id }})" />
                                            @endcan

                                            @can('Configurer un code de travail')
                                                @if ($codeTravail->isConfigurable())
                                                    <x-action-button type="outline-primary" icon="fas fa-cog"
                                                        tooltip="Configuration"
                                                        href="{{ route('rhfeuilledetempsconfig.configure', $codeTravail->id) }}" />
                                                @endif
                                            @endcan
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
            </x-table-card>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card>
                {{-- Filtre par code --}}
                <div class="mb-3">
                    <label for="searchCode" class="form-label">Code</label>
                    <input type="text" id="searchCode" class="form-control" placeholder="Rechercher par code..."
                        wire:model.defer="searchCode">
                </div>

                {{-- Filtre par libellé --}}
                <div class="mb-3">
                    <label for="searchLibelle" class="form-label">Libellé du code</label>
                    <input type="text" id="searchLibelle" class="form-control"
                        placeholder="Rechercher par libellé..." wire:model.defer="searchLibelle">
                </div>

                {{-- Filtre par catégorie --}}
                <div class="mb-3">
                    <label for="filterCategorie" class="form-label">Catégorie</label>
                    <select id="filterCategorie" class="form-select" wire:model.defer="filterCategorie">
                        <option value="">-- Toutes --</option>
                        @foreach ($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </x-filter-card>
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-clipboard-list me-2"></i>
                            {{ $editingId ? 'Modifier le Code de travail' : 'Nouveau Code de travail' }}
                        </h5>
                        {{-- Utilisation du composant pour le bouton de fermeture --}}
                        <x-action-button type="close" wire-click="closeModal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rh-config::code-travail-form :codeTravailId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Détail Code de travail --}}
    @if ($showDetail && $detailCodeTravail)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails du Code de travail
                        </h5>
                        {{-- Bouton de fermeture avec composant --}}
                        <x-action-button type="close" wire-click="closeDetailModal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-clipboard-list me-2"></i>Informations du Code</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Code :</strong></td>
                                        <td><code
                                                class="bg-light px-2 py-1 rounded">{{ $detailCodeTravail->code }}</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Libellé :</strong></td>
                                        <td>{{ $detailCodeTravail->libelle }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Catégorie :</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-info">{{ $detailCodeTravail->categorie->intitule }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Configurable :</strong></td>
                                        <td>
                                            @if ($detailCodeTravail->isConfigurable())
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
                        {{-- Utilisation des composants pour les boutons du footer --}}
                        <div class="d-flex gap-2">
                            <x-action-button type="secondary" text="Fermer" wire-click="closeDetailModal" />
                            @can('Modifier un code de travail')
                                <x-action-button type="success" icon="fas fa-edit" text="Modifier"
                                    wire-click="showEditModal({{ $detailCodeTravail->id }})" />
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
