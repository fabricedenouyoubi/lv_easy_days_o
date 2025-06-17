<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal --}}
    <div class="row">
        {{-- Colonne principale - Tableau --}}
        <div class="col-lg-8">
            <x-table-card 
                title="Liste des codes de travail"
                icon="fas fa-clipboard-list"
                button-text="Nouveau Code"
                button-action="showCreateModal">
                
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
                                            <x-action-button 
                                                type="outline-info"
                                                icon="fas fa-eye"
                                                tooltip="Voir détails"
                                                wire-click="showDetailModal({{ $codeTravail->id }})" />
                                            
                                            <x-action-button 
                                                type="outline-success"
                                                icon="fas fa-edit"
                                                tooltip="Modifier"
                                                wire-click="showEditModal({{ $codeTravail->id }})" />
                                            
                                            @if($codeTravail->isConfigurable())
                                                <x-action-button 
                                                    type="outline-primary"
                                                    icon="fas fa-cog"
                                                    tooltip="Configuration"
                                                    href="{{ route('rhfeuilledetempsconfig.configure', $codeTravail->id) }}" />
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
            </x-table-card>
        </div>

        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card>
                {{-- Filtre par code --}}
                <div class="mb-3">
                    <label for="searchCode" class="form-label">Code</label>
                    <input type="text" 
                           id="searchCode"
                           class="form-control" 
                           placeholder="Rechercher par code..." 
                           wire:model.defer="searchCode">
                </div>

                {{-- Filtre par libellé --}}
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
            </x-filter-card>
        </div>
    </div>
</div>