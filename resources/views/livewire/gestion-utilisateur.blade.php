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
                            Liste des utilisateurs
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body row">
                <!-- Tableau des années financières -->
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle table-hover align-middle table-nowrap mb-0"
                        id="employes" aria-describedby="Liste des utilisateurs">

                        <thead class="table-light">
                            <tr>
                                <th class="orderable" scope="col">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Nom</span>
                                    </a>
                                </th>

                                <th class="orderable" scope="col">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Email</span>
                                    </a>
                                </th>

                                <th class="orderable" scope="col">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Groupe</span>
                                    </a>
                                </th>

                                <th scope="col" class="py-3">
                                    <span class="fw-bold">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($utilisateurs as $utilisateur)
                                <tr class="odd">
                                    <td class="py-2">
                                        {{ $utilisateur->name }}
                                    </td>

                                    <td class="py-2">
                                        {{ $utilisateur->email }}
                                    </td>
                                    <td class="py-2">
                                        <ul class="ps-0">
                                            @foreach ($utilisateur->groups as $group)
                                                <li class="list-group-item px-0 border-0">
                                                    {{ $group?->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a class="btn btn-sm bg-gradient btn-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                aria-label="Groupes" data-bs-original-title="Modifier le(s) groupe(s) de l'utilisateur">
                                                <span class="mdi mdi-account"></span>
                                            </a>
                                            <a class="btn btn-sm bg-gradient btn-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="Permissions"
                                                title="Permission de l'utilisateur"
                                                wire:click="show_user_modal('{{ $utilisateur->name }}', {{ $utilisateur->id }})">
                                                <span class="mdi mdi-cancel"></span>
                                            </a>
                                            <a class="btn btn-sm bg-gradient btn-success" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="Group_permission"
                                                data-bs-original-title="Reinitialiser les permisions de groupe" wire:click="reset_group_permission({{ $utilisateur->id }})">
                                                <span class="mdi mdi-refresh"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $utilisateurs->links() }}
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
                    <form class="filter-form" wire:submit.prevent="get_utilisateurs">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_matricule" class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" placeholder="Rechercher par email"
                                        class="textinput form-control" id="email" wire:model="email_searched">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_nom" class="mb-3">
                                    <label for="id_nom" class="form-label"> Nom </label>
                                    <input type="text" name="nom" placeholder="Rechercher par nom"
                                        class="textinput form-control" id="id_nom" wire:model="name_searched">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_prenom" class="mb-3">
                                    <label for="groupe" class="form-label">Goupe</label>
                                    <input type="text" name="groupe" placeholder="Rechercher par groupe"
                                        class="textinput form-control" id="groupe" wire:model="groupe_searched">
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
    @if ($userPermissionModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-xl w-100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Permissions <span class="fw-bold">{{ $userName }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_user_modal()"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:permission-utilisateur :userId="$userId" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
