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

                <div class="row">
                    <div class="card p-0 shadow-sm rounded-3 border-0 col-12 col-md-3">
                        <div class="card-header bg-transparent border-bottom py-3 px-3">
                            <h5 class="fw-bold d-flex align-items-center">
                                <i class="fa fa-th-list me-2 text-primary"></i>
                                Navigation
                            </h5>
                        </div>
                        <div class="card-body p-0 m-0">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link border-0 rounded-0 w-100 py-3 px-4 d-flex align-items-center information active"
                                    id="v-pills-informations-tab" data-bs-toggle="pill" href="#v-pills-informations"
                                    role="tab" aria-controls="v-pills-informations" aria-selected="true"
                                    tabindex="-1" wire:click='toogle_info' wire:ignore>
                                    <i class="fa fa-user me-2 text-primary"></i>
                                    Informations personnelles
                                </a>

                                <a class="nav-link border-0 rounded-0 py-3 px-4 d-flex align-items-center"
                                    id="v-pills-password-tab" data-bs-toggle="pill" href="#v-pills-password"
                                    role="tab" aria-controls="v-pills-password" aria-selected="false"
                                    wire:click='toogle_pwd' wire:ignore>
                                    <i class="fa fa-key me-2 text-primary"></i>
                                    Modifier mot de passe
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($showInfoEdit)
                        {{-- Affichage des info personnelles d'un employé --}}
                        <div id="profil-body" class="col-12 col-md-9">
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
                                                <p class="mb-0 fs-6">{{ $employe->nombre_d_heure_semaine }}</p>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Formulaire de modification du mot de passe --}}
                        <div class="col-12 col-md-9">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-key me-2 text-primary fs-5"></i>
                                                <h4 class="card-title fw-bold">Changer le mot de passe</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form wire:submit.prevent="changePassword">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="div_id_oldpassword" class="mb-3"> <label
                                                        for="id_oldpassword" class="form-label requiredField">
                                                        Mot de passe actuel<span
                                                            class="asteriskField text-danger">*</span>
                                                    </label>
                                                    <input type="password" name="oldpassword"
                                                        placeholder="Mot de passe actuel"
                                                        autocomplete="Mot de passe actuel"
                                                        class="passwordinput form-control @error('current_password') is-invalid @enderror"
                                                        id="id_oldpassword" wire:model.defer="current_password">
                                                    @error('current_password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    </div">
                                                </div>
                                                <div id="div_id_password1" class="mb-3"> <label for="id_password1"
                                                        class="form-label requiredField">
                                                        Nouveau mot de passe<span
                                                            class="asteriskField text-danger">*</span> </label>
                                                    <input type="password" name="password1"
                                                        placeholder="Nouveau mot de passe" autocomplete="new-password"
                                                        class="passwordinput form-control @error('new_password') is-invalid @enderror"
                                                        id="id_password1" wire:model.defer="new_password">
                                                    @error('new_password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    <div id="id_password1_helptext" class="form-text">
                                                        <ul>
                                                            <li>Votre mot de passe doit contenir au moins une lettre
                                                            </li>
                                                            <li>Votre mot de passe doit contenir au moins 8 caractères.
                                                            </li>
                                                            <li>Votre mot de passe doit contenir au moins un chiffre.
                                                            </li>
                                                            <li>Votre mot de passe doit contenir des majuscules et des
                                                                minuscules.</li>
                                                            <li>Votre mot de passe doit contenir au moins un caractère
                                                                spécial.</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div id="div_id_password2" class="mb-3"> <label for="id_password2"
                                                        class="form-label requiredField">
                                                        Nouveau mot de passe (encore)<span
                                                            class="asteriskField text-danger">*</span>
                                                    </label>
                                                    <input type="password" name="password2"
                                                        placeholder="Nouveau mot de passe (encore)"
                                                        class="passwordinput form-control @error('new_password_confirmation') is-invalid @enderror"
                                                        id="id_password2"
                                                        wire:model.defer="new_password_confirmation">
                                                    @error('new_password_confirmation')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-12">
                                            <x-action-button type="primary" icon="fa fa-edit me-2" size="sm"
                                                text="Changer le
                                                mot de passe"
                                                typeButton='submit' />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12" id="profil-body">

            <x-table-card title="Historique des gestionnaires" icon="fas fa-users"
                button-text="Assigner un gestionnaire" button-action="{{ auth()->user()->can('Changer Gestionnaire Employé')  ? 'showGestModal' : ''}}">
                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Gestionnaire</th>
                                <th>DateDeDebut</th>
                                <th>DateDeFin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gestionnaire_historique as $gestionnaire)
                                <tr>
                                    <td>
                                        <span>{{ $gestionnaire->gestionnaire?->nom . ' ' . $gestionnaire->gestionnaire?->prenom }}</span>
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
                                        <p class="text-muted mb-0">Aucune Historique trouvé</p>
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

    {{-- formulaire d'ajout d'un gestionnaire a un employé --}}
    @if ($showGestM)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-users"></i> Ajouter un gestionnaire à l'employé</h5>
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
</div>
