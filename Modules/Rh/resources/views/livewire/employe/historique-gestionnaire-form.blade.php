<form wire:submit.prevent="saveHist">
    <div id="div_id_gestionnaire" class="mb-3"> <label for="id_gestionnaire" class="form-label requiredField">
            Nouveau gestionnaire<span class="asteriskField text-danger">*</span> </label> <select name="gestionnaire"
            class="select form-select @error('gestionnaire')  is-invalid @enderror" id="id_gestionnaire"
            wire:model="gestionnaire">
            <option value="" selected="">---------</option>
            @foreach ($gestionnaire_list as $gestionnaire)
                <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom.' ' . $gestionnaire->prenom }}</option>
            @endforeach
        </select>
        @error('gestionnaire')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div id="div_id_dateDebut" class="mb-3"> <label for="id_dateDebut"
            class="form-label requiredField @error('dateDebut')  is-invalid @enderror">
            Date de debut du nouveau gestionnaire<span class="asteriskField text-danger">*</span> </label> <input
            type="datetime-local" name="dateDebut" class="datetimeinput form-control" id="id_dateDebut"
            wire:model="dateDebut">
        @error('dateDebut')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>


    <div class="modal-footer">
        <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel' text="Annuler" />
        <x-action-button type="success" icon="fas fa-save me-2" size="md" text="Créer" typeButton='submit' />
    </div>
</form>
