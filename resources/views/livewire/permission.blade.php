<div class="row">
    <div class="card col-12 col-md-9">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-cancel fill-white me-2 fs-5"></i>
                        Liste des permissions
                    </h4>
                </div>
            </div>
        </div>
        <div class="card-body row">

            <div class="table-responsive">
                <table class="table table-nowrap align-middle table-hover align-middle table-sm mb-0 "
                    aria-describedby="Liste des permissions">

                    <thead class="table-light">
                        <tr>
                            <th class="orderable" scope="col">
                                <a class="text-decoration-none text-dark d-flex align-items-center">
                                    <span class="me-1">Nom</span>
                                </a>
                            </th>

                            <th class="orderable" scope="col">
                                <a class="text-decoration-none text-dark d-flex align-items-center">
                                    <span class="me-1">Type de permission</span>
                                </a>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr class="odd">
                                <td class="py-2">
                                    {{ $permission->name }}
                                </td>

                                <td class="py-2">
                                    {{ $permission->module }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <i class="mdi mdi-cancel h1 text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune Permission trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>


    <div class="col-12 col-md-3">
        <!-- Filters Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 d-flex align-items-center">
                        <i class="fa fa-filter me-2 text-primary"></i>
                        Filtres
                    </h6>
                </div>
            </div>


            <div class="card-body pt-2 pb-3">
                <form class="filter-form" wire:submit.prevent="get_permission">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="div_id_matricule" class="mb-3">
                                <label for="id_matricule" class="form-label">Nom de la permission</label>
                                <input type="text" name="name" placeholder="Rechercher par nom"
                                    class="textinput form-control" id="id_matricule" wire:model="name_searched">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="div_id_nom" class="mb-3">
                                <label for="type" class="form-label"> Type de la permission</label>
                                <input type="text" name="nom" placeholder="Rechercher par type"
                                    class="textinput form-control" id="type" wire:model="type_searched">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2 btn-sm">
                                    <span class="mdi mdi-filter"></span>
                                    Filtrer
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetFilter">
                                    <span class="mdi mdi-refresh"></span>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
