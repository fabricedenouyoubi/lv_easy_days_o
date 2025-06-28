<div class="row">
    {{-- Messages de feedback --}}
    <x-alert-messages />
    <div class="col-12">
        <div class="d-flex mb-2 gap-2 justify-content-between">
            <div class="d-flex gap-2">
                <x-action-button type="secondary" icon="fas fa-object-group me-2" size="md" wireClick='select_all'
                    text="Tout selectionner" />
                <x-action-button type="success" icon="far fa-object-ungroup me-2" size="md" wireClick='deselect_all'
                    text="Tout déselectionner" />
            </div>
            @can('Modifier Permissions Groupe')
                <div>
                    <x-action-button type="primary" icon="far fa-save me-2" size="md" wireClick='set_group_permission'
                        text="Enregistrer" loading="true" loading-target="set_group_permission" />
                </div>
            @endcan
        </div>
        <div class="accordion table-hover" id="accordionFlushExample">
            @forelse ($permissionGroups as $module => $group_permissions)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-heading{{ $loop->iteration }}">
                        <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse{{ $loop->iteration }}" aria-expanded="false"
                            aria-controls="flush-collapse{{ $loop->iteration }}">
                            <span>
                                Module <strong>{{ strtoupper(Str::camel($module)) }}</strong>
                            </span>
                        </button>
                    </h2>
                    <div id="flush-collapse{{ $loop->iteration }}" class="accordion-collapse collapse"
                        aria-labelledby="flush-heading{{ $loop->iteration }}" data-bs-parent="#accordionFlushExample"
                        wire:ignore.self>
                        <div class="accordion-body text-muted">
                            <div class="table-responsive">
                                <table
                                    class="table table-nowrap align-middle table-hover align-middle table-nowrap mb-0"
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
                                                    <span class="me-1">Attribué</span>
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($group_permissions as $permission)
                                            <tr class="">
                                                <td class="">
                                                    {{ $permission->name }}
                                                </td>
                                                {{-- <td class="">
                                                    <input type="checkbox" name="checkedPermissions"
                                                        value="{{ $permission->id }}" @checked($role->hasPermissionTo($permission->name)) />
                                                </td> --}}
                                                <td>
                                                    <input class="" type="checkbox"
                                                        id="element_{{ $permission->name }}"
                                                        value="{{ $permission->name }}"
                                                        wire:model.live="checkedPermissions">
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
                        </div>
                    </div>
                </div>
            @empty
                <div class="d-block justify-content-center align-items-center text-center">
                    <i class="fas fa-ban fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucune Permission trouvée</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
