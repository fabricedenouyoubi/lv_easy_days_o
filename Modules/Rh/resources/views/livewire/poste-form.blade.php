<form wire:submit.prevent='save'>
    <div class="mb-3">
        <label for="libelle" class="form-label requiredField">
            Libelle<span class="asteriskField text-danger">*</span>
        </label>
        <input type="text" name="libelle" id="libelle" wire:model="libelle"
            class="form-control @error('libelle') is-invalid @enderror">
        @error('libelle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($posteId)
        <div class="mb-3">
            <div class="mb-3 form-check">
                <input type="checkbox" wire:model="actif" class="checkboxinput form-check-input" id="actif">
                <label>Actif</label>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <label for="description" class="form-label requiredField">
            Description<span class="asteriskField text-danger">*</span>
        </label>
        <textarea name="description" id="description" wire:model="description" rows="5"
            class="form-control @error('description') is-invalid @enderror">
    </textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal"
            wire:click='cancel'>actionCancel</button>
        <button type="submit" class="btn btn-sm btn-outline-primary">actionSave</button>
    </div>
</form>
