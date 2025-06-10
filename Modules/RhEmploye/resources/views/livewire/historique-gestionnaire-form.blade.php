<form wire:submit.prevent="saveHist">
    <div id="div_id_gestionnaire" class="mb-3"> <label for="id_gestionnaire" class="form-label requiredField">
            Nouveau gestionnaire<span class="asteriskField">*</span> </label> <select name="gestionnaire"
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
            class="form-label requiredField @error('gestionnaire')  is-invalid @enderror">
            Date de debut du nouveau gestionnaire<span class="asteriskField">*</span> </label> <input type="datetime-local"
            name="dateDebut" class="datetimeinput form-control" id="id_dateDebut" wire:model="dateDebut">
        @error('dateDebut')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal"
            wire:click="cancel">actionCancel</button>
        <button type="submit" class="btn btn-sm btn-outline-primary">actionSave</button>
    </div>
</form>
