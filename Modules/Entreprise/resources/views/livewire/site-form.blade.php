<div>
    <form wire:submit.prevent="save">
        <!-- Informations du Site -->
        <div class="mb-4">
            <h6 class="mb-3">
                <i class="fas fa-building me-2"></i>
                Informations du Site
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du site <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="name"
                               class="form-control @error('name') is-invalid @enderror" 
                               wire:model="name"
                               placeholder="Nom du site">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description"
                                  class="form-control @error('description') is-invalid @enderror" 
                                  wire:model="description"
                                  rows="3"
                                  placeholder="Description du site"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <div class="mb-4">
            <h6 class="mb-3">
                <i class="fas fa-map-marker-alt me-2"></i>
                Adresse
            </h6>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="rue" class="form-label">Rue <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="rue"
                               class="form-control @error('rue') is-invalid @enderror" 
                               wire:model="rue"
                               placeholder="Adresse de la rue">
                        @error('rue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="appartement" class="form-label">Appartement</label>
                        <input type="text" 
                               id="appartement"
                               class="form-control @error('appartement') is-invalid @enderror" 
                               wire:model="appartement"
                               placeholder="Apt, Bureau...">
                        @error('appartement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="ville" class="form-label">Ville <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="ville"
                               class="form-control @error('ville') is-invalid @enderror" 
                               wire:model="ville"
                               placeholder="Ville">
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="code_postal" class="form-label">Code postal <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="code_postal"
                               class="form-control @error('code_postal') is-invalid @enderror" 
                               wire:model="code_postal"
                               placeholder="Code postal">
                        @error('code_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts -->
        <div class="mb-4">
            <h6 class="mb-3">
                <i class="fas fa-phone me-2"></i>
                Contacts
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                        <input type="tel" 
                               id="telephone"
                               class="form-control @error('telephone') is-invalid @enderror" 
                               wire:model="telephone"
                               placeholder="+237XXXXXXXXX">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="telephone_pro" class="form-label">Téléphone Pro</label>
                        <input type="tel" 
                               id="telephone_pro"
                               class="form-control @error('telephone_pro') is-invalid @enderror" 
                               wire:model="telephone_pro"
                               placeholder="+237XXXXXXXXX">
                        @error('telephone_pro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="telephone_pro_ext" class="form-label">Extension</label>
                        <input type="text" 
                               id="telephone_pro_ext"
                               class="form-control @error('telephone_pro_ext') is-invalid @enderror" 
                               wire:model="telephone_pro_ext"
                               placeholder="123">
                        @error('telephone_pro_ext')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" wire:click="cancel">
                <i class="fas fa-times me-2"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>
                {{ $siteId ? 'Modifier' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
