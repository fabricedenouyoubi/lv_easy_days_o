<form wire:submit.prevent="saveHist">
    <div id="heure" class="mb-3"> <label for="heure" class="form-label requiredField">
            Nouvelle Heure<span class="asteriskField text-danger">*</span> </label>
            <input type="number" class="form-control  @error('heure')  is-invalid @enderror" wire:model="heure" min="1" id="heure" step="any">
        @error('heure')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div id="div_id_dateDebut" class="mb-3"> <label for="id_dateDebut"
            class="form-label requiredField @error('dateDebut')  is-invalid @enderror">
            Date de debut de la nouvelle heure<span class="asteriskField text-danger">*</span> </label> <input
            type="datetime-local" name="dateDebut" class="datetimeinput form-control" id="id_dateDebut"
            wire:model="dateDebut">
        @error('dateDebut')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>


    <div class="modal-footer">
        <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel' text="Annuler" />
        <x-action-button type="success" icon="fas fa-save me-2" size="md" text="Enregistrer" typeButton='submit' />
    </div>
</form>
