<form wire:submit.prevent="save">
    {{-- Matricule --}}
    <div class="mb-3" id="div_id_matricule">
        <label for="id_matricule" class="form-label">Matricule</label>
        <input type="text" wire:model.defer="matricule" id="id_matricule"
            class="form-control  @error('matricule') is-invalid @enderror">
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
            class="form-control  textinput @error('nom') is-invalid @enderror" required>
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
            class="form-control  textinput @error('prenom') is-invalid @enderror">
        @error('prenom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Date de naissance --}}
    <div class="mb-3" id="div_id_date_de_naissance">
        <label for="id_date_de_naissance" class="form-label">Date de naissance</label>
        <input type="date" wire:model.defer="date_de_naissance" id="id_date_de_naissance"
            class="form-control  dateinput @error('date_de_naissance') is-invalid @enderror">
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
            class="form-control  emailinput @error('email') is-invalid @enderror">
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
                <option value="{{ $group->name }}">{{ $group->name }}</option>
            @endforeach
        </select>
        <div class="form-text">Groupes d'accès</div>
        @error('groups')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Boutons --}}
        <div class="modal-footer">
            <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel' text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md" text="Modifier" typeButton='submit' />
        </div>
</form>
