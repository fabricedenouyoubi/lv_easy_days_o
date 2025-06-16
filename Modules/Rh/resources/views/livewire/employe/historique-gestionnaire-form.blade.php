<form wire:submit.prevent="saveHist">
    <div id="div_id_gestionnaire" class="mb-3"> <label for="id_gestionnaire" class="form-label requiredField">
            Nouveau gestionnaire<span class="asteriskField text-danger">*</span> </label> <select name="gestionnaire"
            class="select form-select @error('gestionnaire')  is-invalid @enderror" id="id_gestionnaire"
            wire:model="gestionnaire">
            <option value="" selected="">---------</option>
            @foreach ($gestionnaire_list as $gestionnaire)
                <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->name }}</option>
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
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" wire:click="cancel">
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
