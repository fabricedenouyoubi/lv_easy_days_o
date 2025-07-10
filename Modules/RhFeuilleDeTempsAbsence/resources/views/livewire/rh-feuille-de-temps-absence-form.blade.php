<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Formulaire de demande d'absence --}}
    <form wire:submit.prevent="save">
        <h6 class="text-muted small text-uppercase mb-2"><i class="fas fa-calendar-alt me-1"></i>Informations de la
            demande
            d'absence</h6>
        <div class="mb-3">
            <label for="CodeTraveil" class="form-label requiredField">
                Type d'absence <span class="asteriskField text-danger">*</span> </label> <select name="CodeTraveil"
                class="select form-select @error('code_de_travail_id')  is-invalid @enderror" id="CodeTraveil"
                wire:model="code_de_travail_id">
                <option value="" selected="">---------</option>
                @foreach ($type_absence_list as $type_absence)
                    <option value="{{ $type_absence->id }}">{{ $type_absence->libelle }}</option>
                @endforeach
            </select>
            @error('code_de_travail_id')
                <span class="form-text text-danger">{{ $message }}</span>
            @enderror
            <div id="id_code_de_travail_helptext" class="form-text">Sélectionnez le type d'absence</div>
        </div>

        <h6 class="text-muted small text-uppercase mb-2 mt-4"><i class="fas fa-clock me-1"></i>Période d'absence</h6>
        <div class="mb-3"> <label for="id_dateDebut"
                class="form-label requiredField @error('dateDebut')  is-invalid @enderror">
                Date de debut <span class="asteriskField text-danger">*</span> </label> <input type="datetime-local"
                name="date_debut" class="datetimeinput form-control" id="id_dateDebut" wire:model="date_debut">
            @error('date_debut')
                <span class="form-text text-danger">{{ $message }}</span>
            @enderror
            <div id="flatpickr_start_helptext" class="form-text">Date et heure du début de l'absence</div>
        </div>

        <div class="mb-3"> <label for="id_dateFin"
                class="form-label requiredField @error('dateDebut')  is-invalid @enderror">
                Date de fin <span class="asteriskField text-danger">*</span> </label> <input type="datetime-local"
                name="date_fin" class="datetimeinput form-control" id="id_dateFin" wire:model="date_fin">
            @error('date_fin')
                <span class="form-text text-danger">{{ $message }}</span>
            @enderror
            <div id="flatpickr_start_helptext" class="form-text">Date et heure du début de l'absence</div>
        </div>

        <div class="mb-3"> <label for="heure_par_jour" class="form-label requiredField">
                Heures par jour <span class="asteriskField text-danger">*</span> </label>
            <input type="number" class="form-control  @error('heure_par_jour')  is-invalid @enderror"
                wire:model="heure_par_jour" min="0" id="heure_par_jour" step="any" max="8">
            @error('heure_par_jour')
                <span class="form-text text-danger">{{ $message }}</span>
            @enderror
            <div id="id_heure_par_jour_helptext" class="form-text">Nombre d'heures d'absence par jour (max 8)</div>
        </div>

        <h6 class="text-muted small text-uppercase mb-2 mt-4"><i class="fas fa-info-circle me-1"></i>Informations
            additionnelles</h6>
        <div id="description" class="mb-3"> <label for="heure" class="form-label requiredField">
                Description</label>
            <textarea name="description" class="form-control  @error('description')  is-invalid @enderror" cols="30"
                rows="10" wire:model="description" id="description">
        </textarea>
            @error('description')
                <span class="form-text text-danger">{{ $message }}</span>
            @enderror
            <div id="id_description_helptext" class="form-text">Raison ou détails supplémentaires de la demande</div>
        </div>

        <div class="modal-footer">
            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                wireClick="{{ $demande_absence_id ? 'resetAll' : 'cancel' }}" text="Annuler" />
            <x-action-button type="success" icon="fas fa-save me-2" size="md"
                text="{{ $demande_absence_id ? 'Modifier' : 'Enregistrer' }}" typeButton='submit' loading="true"
                loading-target="save" />
        </div>
    </form>
</div>
