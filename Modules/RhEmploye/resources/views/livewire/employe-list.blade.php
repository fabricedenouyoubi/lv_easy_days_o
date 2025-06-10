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
                        <h4 class="card-title mb-0">
                            <i class="icon nav-icon" data-eva="people-outline"></i>
                            Liste des employés
                        </h4>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="showCreateModal">
                            <i class="mdi mdi-plus" class="fill-white me-2"></i>
                            Nouveau employé
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body row">
                <!-- Tableau des années financières -->
                <div class="table-responsive">

                    <table class="table table-nowrap align-middle table-hover align-middle table-nowrap mb-0"
                        id="employes" aria-describedby="Liste des employes">

                        <thead class="table-light">
                            <tr>
                                <th class="orderable" scope="col" class="py-3">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Matricule</span>
                                    </a>
                                </th>

                                <th class="orderable" scope="col" class="py-3">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Nom</span>
                                    </a>
                                </th>

                                <th class="orderable" scope="col" class="py-3">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Prenom</span>
                                    </a>

                                </th>

                                <th class="orderable" scope="col" class="py-3">
                                    <a class="text-decoration-none text-dark d-flex align-items-center">
                                        <span class="me-1">Gestionnaire</span>
                                    </a>
                                </th>

                                <th scope="col" class="py-3">
                                    <span class="fw-bold">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($employes as $employe)
                                <tr class="odd" class="border-bottom">
                                    <td class="py-2">
                                        {{ $employe->matricule }}
                                    </td>

                                    <td class="py-2">
                                        {{ $employe->nom }}
                                    </td>

                                    <td class="py-2">
                                        {{ $employe->prenom }}
                                    </td>

                                    <td class="py-2">
                                        {{ $employe->gestionnaire?->nom  ?? '---'}}
                                    </td>

                                    <td class="py-2">
                                        <div class='d-flex gap-2 justify-content-center'>
                                            <a class='btn btn-sm bg-gradient btn-primary' data-bs-toggle='tooltip'
                                                data-bs-placement='top' title='actionDetails' href="{{ route('rh-employe.show', $employe->id) }}">
                                                <span class="mdi mdi-account"></span>
                                            </a>
                                            <a class='btn btn-sm bg-gradient btn-success' data-bs-toggle='tooltip'
                                                data-bs-placement='top' title='feuille de temps'>
                                                <span class="mdi mdi-clock-outline"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $employes->links() }}
                </div>
            </div>
        </div>

        {{-- Filter Part --}}
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

                {{-- Form  Filter --}}
                <div class="card-body pt-2 pb-3">
                    <form class="filter-form" wire:submit.prevent="getEmployes">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_matricule" class="mb-3">
                                    <label for="id_matricule" class="form-label">Matricule</label>
                                    <input type="text" name="matricule" placeholder="Rechercher par matricule"
                                        class="textinput form-control" id="id_matricule" wire:model="matricule_searched">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_nom" class="mb-3">
                                    <label for="id_nom" class="form-label"> Nom </label>
                                    <input type="text" name="nom" placeholder="Rechercher par nom"
                                        class="textinput form-control" id="id_nom"wire:model="nom_searched">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="div_id_prenom" class="mb-3">
                                    <label for="id_prenom" class="form-label">Prénom</label>
                                    <input type="text" name="prenom" placeholder="Rechercher par prénom"
                                        class="textinput form-control" id="id_prenom" wire:model="prenom_searched">
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-12 mb-3">
                                <div id="div_id_gestionnaire_search" class="mb-3">
                                    <label for="id_gestionnaire_search" class="form-label">
                                        Gestionnaire
                                    </label>
                                    <input type="text"
                                        name="gestionnaire_search"placeholder="Rechercher par gestionnaire"
                                        class="textinput form-control" id="id_gestionnaire_search" wire:model="gestionnaire_searched">
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
                                    <button type="button" class="btn btn-secondary btn-sm"  wire:click="resetFilter">
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

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un nouveau employé</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rhemploye::employe-form />
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
