<div class="row">
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="col-12 col-md-9">
        <div class="card-body row">
            <div class="table-responsive">
                <div class="d-flex mb-2 gap-2 justify-content-between">
                    <div>
                        <button class="btn btn-sm btn-secondary" wire:click="select_all"><span
                                class="mdi mdi-check-all"></span> Tout selectionner</button>
                        <button class="btn btn-sm btn-danger" wire:click="deselect_all"><span
                                class="mdi mdi-tab-unselected"></span> Tout déselectionner</button>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-success" wire:click="set_group_permission">
                            @if ($load_save_permission)
                                <div class="spinner-border fs-5 spinner-border-sm text-light" role="status"></div>
                                Enregistrer
                            @else
                                <span class="mdi mdi-content-save-all"></span>
                                Enregistrer
                            @endif
                        </button>
                    </div>
                </div>
                <form>
                    <table class="table table-nowrap align-middle table-hover align-middle table-nowrap mb-0"
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

                                <th class="orderable" scope="col">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Code</span>
                                    </a>
                                </th>


                                <th class="orderable" scope="col">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Attribué</span>
                                    </a>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr class="odd">
                                    <td class="py-2">
                                        {{ $permission->name }}
                                    </td>

                                    <td class="py-2">
                                        {{ $permission->contentType?->app_label }}
                                    </td>

                                    <td class="py-2">
                                        {{ $permission->codename }}
                                    </td>

                                    <td class="py-2">
                                        <div class="form-check">
                                            <input class="" type="checkbox" id="element_{{ $permission->id }}"
                                                value="{{ $permission->id }}" wire:model.live="checkedPermissions">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
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
                        <div class="col-12 mb-3">
                            <div id="div_id_nom" class="mb-3">
                                <label for="code" class="form-label"> Code de la permission</label>
                                <input type="text" name="nom" placeholder="Rechercher par code"
                                    class="textinput form-control" id="code" wire:model="code_searched">
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
