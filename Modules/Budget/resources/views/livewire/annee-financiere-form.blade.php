<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                <input type="date"
                       id="debut"
                       class="form-control @error('debut') is-invalid @enderror"
                       wire:model="debut">
                @error('debut')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Doit être le 1er avril</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                <input type="date"
                       id="fin"
                       class="form-control @error('fin') is-invalid @enderror"
                       wire:model="fin">
                @error('fin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Doit être le 31 mars de l'année suivante</small>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="mb-3">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input"
                           type="checkbox"
                           id="actif"
                           wire:model="actif">
                    <label class="form-check-label" for="actif">
                        Année active
                    </label>
                </div>
                <small class="form-text text-muted">Une seule année peut être active à la fois</small>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <x-action-button type="secondary" icon="fas fa-times me-2" size="md" wireClick='cancel' text="Annuler"/>
        <x-action-button type="success" icon="fas fa-save me-2" size="md" text="{{ $anneeId ? 'Modifier' : 'Créer' }}" typeButton='submit'/>
    </div>
</form>
