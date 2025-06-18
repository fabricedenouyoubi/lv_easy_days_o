<form wire:submit.prevent="save">
    <div id="name" class="mb-3"> <label for="name"
            class="form-label requiredField @error('name')  is-invalid @enderror">
            Nom du groupe <span class="asteriskField text-danger">*</span> </label> <input
            type="text" name="name" class="datetimeinput form-control" id="name"
            wire:model="name">
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="modal-footer">
        <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel' text="Annuler"/>
        <x-action-button type="success" icon="fas fa-save me-2" size="md" text="{{ $groupId ? 'Modifier' : 'Créer' }}" typeButton='submit'/>
    </div>
</form>
