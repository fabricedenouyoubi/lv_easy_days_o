<div class="row">
    <div class="col-lg-8">
        <x-table-card title="Liste des permissions" icon="fas fa-ban">

            {{-- Contenu du tableau --}}
            <div class="table-responsive">
                <table class="table table-nowrap align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Type de permission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <span>{{ $permission->name }}</span>
                                </td>
                                <td>
                                    <span>{{ $permission->module }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <i class="fas fa-ban fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune Permission trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </x-table-card>
    </div>

    {{-- Colonne latérale - Filtres --}}
    <div class="col-lg-4">
        <x-filter-card filterAction="get_permission">
            {{-- Filtre par nom --}}
            <div class="mb-3">
                <label for="name_searched" class="form-label">Nom</label>
                <input type="text" id="name_searched" class="form-control" placeholder="Rechercher par Nom..."
                    wire:model.defer="name_searched">
            </div>
            {{-- Filtre par type --}}
            <div class="mb-3">
                <label for="type_searched" class="form-label">Type</label>
                <input type="text" id="type_searched" class="form-control" placeholder="Rechercher par Type..."
                    wire:model.defer="type_searched">
            </div>
        </x-filter-card>
    </div>
</div>
