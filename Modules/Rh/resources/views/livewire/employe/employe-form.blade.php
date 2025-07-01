<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Formulaire employe --}}
    <form wire:submit.prevent='save'>
        <h6 class="text-muted small text-uppercase mb-2"><i class="fas fa-id-card me-1"></i>Identité</h6>

        <div class="row">
            <div class="col-sm-6 mb-2">
                <div id="div_id_nom" class="mb-3">
                    <label for="nom" class="form-label form-label fw-semibold requiredField">
                        Nom
                        <span class="asteriskField text-danger">*</span>
                    </label>
                    <input type="text" name="nom" class="form-control  @error('nom') is-invalid @enderror"
                        id="nom" wire:model="nom">
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-2">
                <div id="div_id_prenom" class="mb-3">
                    <label for="id_prenom" class="form-label form-label fw-semibold requiredField">
                        Prénom
                        <span class="asteriskField text-danger">*</span>
                    </label>
                    <input type="text" name="prenom" class="form-control  @error('prenom') is-invalid @enderror"
                        id="id_prenom" wire:model="prenom">
                    @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 mb-2">
                <div id="div_id_date_de_naissance" class="mb-3">
                    <label for="id_date_de_naissance" class="form-label form-label fw-semibold">
                        Date naissance
                    </label>
                    <input type="date" name="date_de_naissance"
                        class="datepicker  dateinput form-control  @error('date_de_naissance') is-invalid @enderror"
                        data-bs-toggle="datepicker" id="id_date_de_naissance" wire:model="date_de_naissance">
                    @error('date_de_naissance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 mb-2">
                <div id="div_id_email" class="mb-3">
                    <label for="id_email" class="form-label form-label fw-semibold requiredField">
                        Email
                        <span class="asteriskField text-danger">*</span>
                    </label>
                    <input type="email" name="email" placeholder="employe@exemple.com"
                        class="form-control  @error('email') is-invalid @enderror" id="id_email" wire:model="email">
                    <div id="id_email_helptext" class="form-text">Email professionnel</div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>
        <h6 class="text-muted small text-uppercase mt-2 mb-2">
            <i class="fas fa-briefcase me-1"></i>
            Informations professionnelles
        </h6>
        <div class="row align-items-center">
            <div class="col-sm-6 mb-2">
                <div id="div_id_matricule" class="mb-3">
                    <label for="id_matricule" class="form-label form-label fw-semibold">
                        Matricule
                    </label>
                    <input type="text" name="matricule"
                        class="form-control  textinput  @error('matricule') is-invalid @enderror" id="id_matricule"
                        wire:model="matricule">
                    <div id="id_matricule_helptext" class="form-text">ID unique</div>
                    @error('matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 mb-2">
                <div id="div_id_nombre_d_heure_semaine" class="mb-3"> <label for="id_nombre_d_heure_semaine"
                        class="form-label form-label fw-semibold requiredField">
                        H/semaine <span class="asteriskField text-danger">*</span> </label>
                    <input type="number" name="nombre_d_heure_semaine" placeholder="35"
                        class="form-control  @error('nombre_d_heure_semaine') is-invalid @enderror"
                        id="id_nombre_d_heure_semaine" wire:model="nombre_d_heure_semaine" min="1"
                        step="any">
                    <div id="id_nombre_d_heure_semaine_helptext" class="form-text">Heures standard</div>
                    @error('nombre_d_heure_semaine')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-2">
                <label for="gestionnaire_id" class="form-label requiredField">
                    Gestionnaire <span class="asteriskField text-danger">*</span> </label> <select
                    name="gestionnaire_id" class="select form-select @error('gestionnaire_id')  is-invalid @enderror"
                    id="gestionnaire_id" wire:model="gestionnaire_id">
                    <option value="" selected="">---------</option>
                    @foreach ($gestionnaire_list as $gestionnaire)
                        <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom . ' ' . $gestionnaire->prenom }}
                        </option>
                    @endforeach
                </select>
                @error('gestionnaire')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 mb-2">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <div class="form-check">
                        <br>
                        <input class="form-check-input" type="checkbox" id="est_gestionnaire"
                            wire:model="est_gestionnaire">
                        <label class="form-check-label font-size-13" for="est_gestionnaire">
                            Est un gestionnaire ?
                        </label>
                        @error('est_gestionnaire')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>
        <hr class="my-2">
        <h6 class="text-muted small text-uppercase mb-2"><i class="fas fa-shield-alt me-1"></i>Paramètres
            de sécurité</h6>
        <div class="row ">
            <div class="col-12 mb-2">
                <div id="div_id_group" class="mb-3"> <label for="id_group"
                        class="form-label form-label fw-semibold requiredField">Groupes
                        <span class="asteriskField text-danger">*</span> </label>
                    <select name="group" class="form-select form-select-sm @error('groups') is-invalid @enderror"
                        id="id_group" multiple wire:model="groups">
                        @foreach ($groups_list as $group)
                            <option value="{{ $group->name }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <div id="id_group_helptext" class="form-text">Groupes d'accès</div>
                    @error('groups')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                </div>
                <div class="text-muted small mt-1">Les groupes déterminent les permissions de cet employé
                </div>
            </div>

        </div>


        <div class="modal-footer">
            <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel'
                text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md" text="Créer"
                typeButton='submit' />
        </div>
    </form>
</div>
