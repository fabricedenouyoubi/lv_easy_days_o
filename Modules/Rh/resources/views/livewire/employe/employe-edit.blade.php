<form wire:submit.prevent="save">
    {{-- Matricule --}}
    <div class="mb-3" id="div_id_matricule">
        <label for="id_matricule" class="form-label">Matricule</label>
        <input type="text" wire:model.defer="matricule" id="id_matricule"
            class="form-control form-control-sm @error('matricule') is-invalid @enderror">
        <div class="form-text">ID unique</div>
        @error('matricule')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Nom --}}
    <div class="mb-3" id="div_id_nom">
        <label for="id_nom" class="form-label requiredField">
            Nom <span class="asteriskField text-danger">*</span>
        </label>
        <input type="text" wire:model.defer="nom" id="id_nom"
            class="form-control form-control-sm textinput @error('nom') is-invalid @enderror" required>
        @error('nom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Prénom --}}
    <div class="mb-3" id="div_id_prenom">
        <label for="id_prenom" class="form-label requiredField">
            Prénom <span class="asteriskField text-danger">*</span>
        </label>
        <input type="text" wire:model.defer="prenom" id="id_prenom"
            class="form-control form-control-sm textinput @error('prenom') is-invalid @enderror">
        @error('prenom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Date de naissance --}}
    <div class="mb-3" id="div_id_date_de_naissance">
        <label for="id_date_de_naissance" class="form-label">Date de naissance</label>
        <input type="date" wire:model.defer="date_de_naissance" id="id_date_de_naissance"
            class="form-control form-control-sm dateinput @error('date_de_naissance') is-invalid @enderror">
        @error('date_de_naissance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3" id="div_id_email">
        <label for="id_email" class="form-label Field">
            Email <span class="asteriskField text-danger">*</span>
        </label>
        <input type="email" wire:model.defer="email" id="id_email" maxlength="200" placeholder="employe@exemple.com"
            class="form-control form-control-sm emailinput @error('email') is-invalid @enderror">
        <div class="form-text">Email professionnel</div>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Groupes --}}
    <div class="mb-3" id="div_id_group">
        <label for="id_group" class="form-label requiredField">
            Groupes <span class="asteriskField text-danger">*</span>
        </label>
        <select wire:model.defer="groups" id="id_group" multiple
            class="form-select form-select-sm selectmultiple @error('groups') is-invalid @enderror"
            style="height: 100px;">
            @foreach ($groups_list as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
        <div class="form-text">Groupes d'accès</div>
        @error('groups')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Boutons --}}
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" wire:click="cancel" data-bs-dismiss="modal">
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
                </svg>Annuler</button>

            <button type="submit" class="btn btn-sm btn-primary">
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
                </svg>Enregistrer</button>
        </div>
</form>
