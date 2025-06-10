<form wire:submit.prevent='save'>
    <div class="alert alert-info py-1 small mb-2">Veuillez remplir tous les champs obligatoires marqués
        d'un astérisque (*)</div>
    <h6 class="text-muted small text-uppercase mb-2"><i class="fas fa-id-card me-1"></i>Identité</h6>
    <div class="row ">
        <div class="col-sm-6 mb-2">
            <div id="div_id_nom" class="mb-3">
                <label for="nom" class="form-label form-label fw-semibold requiredField">
                    Nom
                    <span class="asteriskField text-danger">*</span>
                </label>
                <input type="text" name="nom"
                    class="form-control form-control-sm @error('nom') is-invalid @enderror" id="nom"
                    wire:model="nom">
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
                <input type="text" name="prenom"
                    class="form-control form-control-sm @error('prenom') is-invalid @enderror" id="id_prenom"
                    wire:model="prenom">
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
                    class="datepicker form-control-sm dateinput form-control  @error('date_de_naissance') is-invalid @enderror"
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
                    class="form-control form-control-sm @error('email') is-invalid @enderror" id="id_email"
                    wire:model="email">
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
    <div class="row">
        <div class="col-sm-6 mb-2">
            <div id="div_id_matricule" class="mb-3">
                <label for="id_matricule" class="form-label form-label fw-semibold">
                    Matricule
                </label>
                <input type="text" name="matricule"
                    class="form-control form-control-sm textinput  @error('matricule') is-invalid @enderror"
                    id="id_matricule" wire:model="matricule">
                <div id="id_matricule_helptext" class="form-text">ID unique</div>
                @error('matricule')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 mb-2">
            <div id="div_id_nombre_d_heure_semaine" class="mb-3"> <label for="id_nombre_d_heure_semaine"
                    class="form-label form-label fw-semibold requiredField">
                    H/semaine<span class="asteriskField text-danger">*</span> </label>
                <input type="number" name="nombre_d_heure_semaine" placeholder="35"
                    class="form-control form-control-sm @error('nombre_d_heure_semaine') is-invalid @enderror"
                    id="id_nombre_d_heure_semaine" wire:model="nombre_d_heure_semaine" min="1">
                <div id="id_nombre_d_heure_semaine_helptext" class="form-text">Heures standard</div>
                @error('nombre_d_heure_semaine')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
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
        <button type="button" class="btn bg-gradient btn-secondary" data-bs-dismiss="modal" wire:click='cancel'>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                class="eva eva-close-outline fill-white align-text-top me-1">
                <g data-name="Layer 2">
                    <g data-name="close">
                        <rect width="24" height="24" transform="rotate(180 12 12)" opacity="0"></rect>
                        <path
                            d="M13.41 12l4.3-4.29a1 1 0 1 0-1.42-1.42L12 10.59l-4.29-4.3a1 1 0 0 0-1.42 1.42l4.3 4.29-4.3 4.29a1 1 0 0 0 0 1.42 1 1 0 0 0 1.42 0l4.29-4.3 4.29 4.3a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42z">
                        </path>
                    </g>
                </g>
            </svg>
            actionCancel
        </button>
        <button type="submit" class="btn bg-gradient btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                class="eva eva-save-outline fill-white align-text-top me-1">
                <g data-name="Layer 2">
                    <g data-name="save">
                        <rect width="24" height="24" opacity="0"></rect>
                        <path
                            d="M20.12 8.71l-4.83-4.83A3 3 0 0 0 13.17 3H6a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-7.17a3 3 0 0 0-.88-2.12zM10 19v-2h4v2zm9-1a1 1 0 0 1-1 1h-2v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2v5a1 1 0 0 0 1 1h4a1 1 0 0 0 0-2h-3V5h3.17a1.05 1.05 0 0 1 .71.29l4.83 4.83a1 1 0 0 1 .29.71z">
                        </path>
                    </g>
                </g>
            </svg>
            actionSave
        </button>
    </div>
</form>
