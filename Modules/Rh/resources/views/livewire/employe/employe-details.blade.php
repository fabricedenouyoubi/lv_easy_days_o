<div>
    <div class="row g-4">
        <!-- Sidebar de profil -->
        <div class="col-12">
            <div class="user-sidebar">
                <!-- Carte de navigation -->
                <div class="card shadow-sm rounded-3 border-0 mb-4 w-100">
                    <div class="card-body p-0">
                        <!-- Background et image de profil -->
                        <div class="position-relative">
                            <div class="profile-bg bg-gradient-primary rounded-top" style="height: 120px; width:100%">
                            </div>
                            <div class="avatar-container d-flex justify-content-center">
                                <div class="avatar-wrapper position-absolute" style="top: 70px;">
                                    <div class="avatar-placeholder d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm border border-3 border-white"
                                        style="width: 100px; height: 100px;">
                                        <i class="fa fa-user-circle text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Informations de l'employé -->
                        <div class="text-center pt-5 pb-3 px-3">
                            <h5 class="fw-bold mb-1 mt-2">{{ $employe->nom . ' ' . $employe->prenom }}</h5>

                        </div>
                    </div>
                </div>

                {{-- Messages de feedback --}}
                <x-alert-messages />

                {{-- Affichage des info personnelles d'un employé --}}
                <div id="profil-body" class="col-12 col-md-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold m-0 d-flex align-items-center">
                                    <i class="fa fa-user-circle me-2 text-primary"></i>
                                    Informations personnelles
                                </h5>
                                @can('Modifier Employé')
                                    <x-action-button type="primary" icon="fa fa-edit me-2" size="sm"
                                        wireClick='showEditModal' text=" Modifier les informations de l'employé" />
                                @endcan
                            </div>
                        </div>
                        <div class="card-body pt-3 pb-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Matricule</h6>
                                        <p class="mb-0 fs-6">{{ $employe->matricule }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Nom</h6>
                                        <p class="mb-0 fs-6">{{ $employe->nom . ' ' . $employe->prenom }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Prénom</h6>
                                        <p class="mb-0 fs-6">{{ $employe->prenom }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Email</h6>
                                        <p class="mb-0 fs-6">{{ $employe->email() }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Jour d'anniversaire</h6>
                                        <p class="mb-0 fs-6">{{ $employe->date_de_naissance }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold  mb-1">Nombre d'heures par semaine</h6>
                                        <p class="mb-0 fs-6">{{ $employe->heure_semaines_employe() }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Gestionnaire</h6>
                                        <p class="mb-0 fs-6">
                                            {{ $employe->gestionnaire?->nom . ' ' . $employe->gestionnaire?->prenom ?? '---' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Groupe</h6>
                                        <div class="mb-0 fs-6">

                                            <ul class="list-group list-group-flush ps-0">
                                                @foreach ($employe->employe_groups() as $group)
                                                    <li class="list-group-item px-0 py-1 border-0">
                                                        {{ $group?->name }}
                                                    </li>
                                                @endforeach
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <h6 class="text-muted fw-bold mb-1">Est un Gestionnaire</h6>
                                        <p class="mb-0 fs-6">
                                            {{ $employe->est_gestionnaire ? 'Oui' : 'Non' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historique des heures par semaine --}}
        <div class="col-12" id="profil-body">
            <x-table-card title="Historique des heures par semaine" icon="fas fa-user-clock"
                buttonIcon="{{ $countHistHeures > 0 ? 'fas fa-edit' : 'fas fa-plus' }}"
                button-text="{{ $countHistHeures > 0 ? 'Modifier l heure' : 'Assigner une heure' }}"
                button-action="{{ auth()->user()->can('Changer Heure Semaine Employé') ? 'showHeuresModal' : '' }}">
                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre d'heure</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($heure_historique as $historique)
                                <tr>
                                    <td>
                                        <span><strong>{{ $historique->nombre_d_heure_semaine }}</strong></span>
                                    </td>
                                    <td>
                                        <span>{{ $historique->date_debut }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $historique->date_fin ?? '---' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune Historique trouvée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="mt-3">
                    {{-- {{ $gestionnaire_historique->links() }} --}}
                </div>
            </x-table-card>
        </div>

        {{-- Historiques des gestionnaire --}}
        <div class="col-12" id="profil-body">
            <x-table-card title="Historique des gestionnaires" icon="fas fa-users"
                buttonIcon="{{ $countHistGestio > 0 ? 'fas fa-edit' : 'fas fa-plus' }}"
                button-text="{{ $countHistGestio > 0 ? 'Modifier le gestionnaire' : 'Assigner un gestionnaire' }}"
                button-action="{{ auth()->user()->can('Changer Gestionnaire Employé') ? 'showGestModal' : '' }}">
                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Gestionnaire</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gestionnaire_historique as $gestionnaire)
                                <tr>
                                    <td>
                                        <span><strong>{{ $gestionnaire->gestionnaire?->nom . ' ' . $gestionnaire->gestionnaire?->prenom }}</strong></span>
                                    </td>
                                    <td>
                                        <span>{{ $gestionnaire->date_debut }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $gestionnaire->date_fin ?? '---' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune Historique trouvée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $gestionnaire_historique->links() }}
                </div>
            </x-table-card>
        </div>
    </div>


    {{-- Formulaire de modificartion de l'utilisateur --}}
    @if ($showModal)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-edit me-2"></i> Mise à jour des informations de
                            l'employé
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="showEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh::employe.employe-edit :employeId='$employe->id' />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- formulaire d'ajout/modification du gestionnaire d'un employé --}}
    @if ($showGestM)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-users"></i>
                            {{ $countHistGestio > 0 ? 'Modifier le gestionnaire' : 'Ajouter un gestionnaire' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="closeGestModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh::employe.historique-gestionnaire-form :employeId='$employe->id' />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- formulaire d'ajout/modification des heures d'un employé --}}
    @if ($showHeuresM)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-users"></i>
                            {{ $countHistHeures > 0 ? 'Modifier l\'' : 'Ajouter l\'' }}
                            heure</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="showHeuresModal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:rh::employe.historique-heures-semaines-form :employeId='$employe->id' />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
