<div>
    <!-- Messages de feedback -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Informations de l'entreprise -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        Informations de l'Entreprise
                    </h4>
                </div>
                <div class="col-auto">
                    @if(!$editing)
                        <button type="button" class="btn btn-primary" wire:click="edit">
                            <i class="fas fa-edit me-2"></i>
                            Modifier
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($editing)
                <!-- Mode édition -->
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                                <input type="text" 
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror" 
                                       wire:model="name"
                                       placeholder="Nom de l'entreprise">
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
                                          rows="4"
                                          placeholder="Description de l'entreprise"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" wire:click="cancel">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            @else
                <!-- Mode affichage -->
                @if($entreprise)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nom de l'entreprise</label>
                                <p class="form-control-plaintext">{{ $entreprise->name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <p class="form-control-plaintext">
                                    {{ $entreprise->description ?: 'Aucune description renseignée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5>Aucune entreprise configurée</h5>
                        <p class="text-muted">Cliquez sur "Modifier" pour entrer les informations de l'entreprise.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
