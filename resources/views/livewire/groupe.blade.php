<div>
    <!-- Messages de feedback -->
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
    <div class="row">
        <div class="card col col-md-9">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0 d-flex align-items-center">
                            <i class="mdi mdi-account-multiple fill-white me-2 fs-4"></i>
                            Liste des groupes
                        </h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="show_add_group_modal">
                            <i class="mdi mdi-plus" class="fill-white me-2"></i>
                            Nouveau groupe
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body row">
                <!-- Tableau des années financières -->
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle table-hover align-middle table-nowrap mb-0 table-sm"
                        id="employes" aria-describedby="Liste des utilisateurs">

                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-3">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Nom</span>
                                    </a>
                                </th>
                                <th scope="col" class="py-3">
                                    <span class="fw-bold">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($groups as $group)
                                <tr class="odd">
                                    <td class="py-2">
                                        {{ $group->name }}
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a class="btn btn-sm bg-gradient btn-success" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="Groupe" title="Modifier le groupe"
                                                data-bs-original-title="Modifier le groupe"
                                                wire:click="show_edit_groupe_modal({{ $group->id }})">
                                                <span class="mdi mdi-account-edit"></span>
                                            </a>
                                            <a class="btn btn-sm bg-gradient btn-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="Group_permission"
                                                title="Permissions du groupe"
                                                data-bs-original-title="Permissions du groupe"
                                                wire:click="show_group_permission_modal({{ $group->id }},'{{ $group->name }}')">
                                                <span class="mdi mdi-cancel"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <i class="mdi mdi-account-multiple h1 text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun Groupe trouvé</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>


        <div class="col">
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
                    <form class="filter-form" wire:submit.prevent="get_groupes">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_matricule" class="mb-3">
                                    <label for="name_searched" class="form-label">Nom</label>
                                    <input type="text" name="name_searched" placeholder="Rechercher par nom"
                                        class="textinput form-control" id="name_searched" wire:model="name_searched">
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

    {{-- Formulaire d'ajout et de modification des groupes --}}
    @if ($show_add_group)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> {{ $groupId ? 'Modifier le groupe ' : 'Ajouter un groupe' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_groupe_add_modal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:groupe-form :groupId="$groupId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tableau des permisssions d'un group --}}
    @if ($group_permission)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-xl w-100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Permissions du groupe <span class="fw-bold">{{ $groupName }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_group_permission_modal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:group-permission :groupId="$groupId" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
