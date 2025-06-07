<div>
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

    <div class="row mb-4">
        <h4 class="mb-0 fw-semibold text-primary">
            <small class="text-muted ms-2 fw-normal">
                <span class="badge bg-light text-dark border">
                    Feuille de temps / Période : 2025-04-01 - 2026-03-31
                </span>
            </small>
        </h4>
    </div>
    <div class="row">
        <div class="col col-md-9">
            <div class="card no-border shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12" id="poste-table">
                            <div class="table-container table-responsive shadow-sm rounded" id="postes-container">

                                <table class="table table-hover align-middle table-nowrap mb-0" id="postes"
                                    aria-describedby="Liste des postes">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="orderable" scope="col" class="py-3">
                                                <a class="text-decoration-none text-dark d-flex align-items-center">
                                                    <span class="me-1">Libelle</span>
                                                    {{-- <i class="fa fa-sort ms-1 text-muted small"></i> --}}
                                                </a>
                                            </th>

                                            <th class="orderable" scope="col" class="py-3">
                                                <a class="text-decoration-none text-dark d-flex align-items-center">
                                                    <span class="me-1">Description</span>
                                                </a>

                                            </th>

                                            <th class="orderable" scope="col" class="py-3">

                                                <a class="text-decoration-none text-dark d-flex align-items-center">
                                                    <span class="me-1">Actif</span>
                                                </a>

                                            </th>

                                            <th scope="col" colspan="2">
                                                <span class="fw-bold">Action</span>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($postes as $poste)
                                            <tr class="even" class="border-bottom">
                                                <td class="py-2">
                                                    {{ $poste->libelle }}
                                                </td>
                                                <td class="py-2">
                                                    {{ $poste->description }}
                                                </td>
                                                <td class="py-2">
                                                    @if ($poste->actif)
                                                        <i class="fa fa-check text-success"></i>
                                                    @else
                                                        <i class="fa fa-times text-danger"></i>
                                                    @endif

                                                </td>
                                                <td class="py-2 d-flex gap-3">
                                                    <button class='btn btn-outline-warning btn-sm'
                                                        wire:click="showEditModal({{ $poste->id }})">
                                                        actionEdit
                                                    </button>
                                                    <button class='btn btn-outline-warning btn-sm'
                                                        wire:click="confirmDelete({{ $poste->id }})">
                                                        actionDelete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-3 mb-3">
                        {{ $postes->links() }}
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <a class="btn btn-sm btn-outline-warning" wire:click="showCreateModal">
                                <i class="fa fa-plus"></i> actionNew
                            </a> &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card no-border shadow mb-2">
                <div class="card-body">
                    <form wire:submit.prevent='getAnneesProperty'>
                        <div id="div_id_libelle" class="mb-3"> <label for="id_libelle" class="form-label">
                                Libelle
                            </label> <input type="text" wire:model="search_libelle" class="textinput form-control"
                                id="id_libelle"> </div>
                        <div id="div_id_description" class="mb-3"> <label for="id_description" class="form-label">
                                Description
                            </label> <input type="text" wire:model="search_decription" class="textinput form-control"
                                id="id_description" wire:model=""> </div>

                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($showModal)
        <div wire:ignore.self id="modal" class="modal fade show d-block" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div id="dialog" class="modal-dialog">
                <div class="modal-content animated-modal">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un nouveau un poste</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh::poste-form :posteId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce Poste ?</p>
                        <p class="text-danger"><small>Cette action est irréversible.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
