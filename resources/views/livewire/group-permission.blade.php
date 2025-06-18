<div class="row">
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <div class="col-12 col-md-9">
        <div class="card-body row">
            <div class="table-responsive">
                <div class="d-flex mb-2 gap-2 justify-content-between">
                    <div>
                        <x-action-button type="secondary" icon="fas fa-object-group me-2" size="md"
                            wireClick='select_all' text="Tout selectionner" />
                        <x-action-button type="success" icon="far fa-object-ungroup me-2" size="md"
                            wireClick='deselect_all' text="Tout déselectionner" />
                    </div>
                    <div>
                        <x-action-button type="primary" icon="far fa-save me-2" size="md"
                            wireClick='set_group_permission' text="Enregistrer" loading="true" loading-target="set_group_permission"/>
                    </div>
                </div>
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
                                    {{ $permission->module }}
                                </td>

                                <td class="py-2">
                                    <div class="form-check">
                                        <input class="" type="checkbox" id="element_{{ $permission->name }}"
                                            value="{{ $permission->name }}" wire:model.live="checkedPermissions">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
                    <input type="text" id="type_searched" class="form-control"
                        placeholder="Rechercher par Type..." wire:model.defer="type_searched">
                </div>
            </x-filter-card>
        </div>
    </div>
</div>
