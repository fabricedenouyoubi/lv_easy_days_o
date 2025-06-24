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
                        <h5 class="fw-bold mb-1 mt-2">{{ auth()->user()->name }}</h5>

                    </div>
                </div>
            </div>

            {{-- Messages de feedback --}}
            <x-alert-messages />

            {{-- Formulaire de modification du mot de passe --}}
            <div class="col-12 col-md-12">
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
                                    <div id="div_id_oldpassword" class="mb-3"> <label for="id_oldpassword"
                                            class="form-label requiredField">
                                            Mot de passe actuel<span class="asteriskField text-danger">*</span>
                                        </label>
                                        <input type="password" name="oldpassword" placeholder="Mot de passe actuel"
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
                                            Nouveau mot de passe<span class="asteriskField text-danger">*</span>
                                        </label>
                                        <input type="password" name="password1" placeholder="Nouveau mot de passe"
                                            autocomplete="new-password"
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
                                            id="id_password2" wire:model.defer="new_password_confirmation">
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
        </div>
    </div>
</div>
