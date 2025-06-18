{{-- DIV RACINE UNIQUE POUR TOUT LE COMPOSANT --}}
<div>
    @if($configuration)
        {{-- Information sur la configuration --}}
        <div class="alert alert-info mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-1">
                        <i class="fas fa-cogs me-2"></i>{{ $configuration->libelle }}
                    </h6>
                    <small class="text-muted">
                        Quota total : <strong>{{ number_format($configuration->quota, 2) }}h</strong> | 
                        Consommé : <strong>{{ number_format($configuration->consomme, 2) }}h</strong> | 
                        Restant : <strong>{{ number_format($configuration->reste, 2) }}h</strong>
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-success fs-6">
                        {{ $nombreEmployesSelectionnes }} employé(s) sélectionné(s)
                    </span>
                </div>
            </div>
        </div>

        {{-- Barre de recherche --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Rechercher un employé..." 
                           wire:model.live.debounce.300ms="searchEmploye">
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-success"
                            wire:click="sauvegarderAffectations"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i>Sauvegarder les affectations
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Sauvegarde...
                        </span>
                    </button>
                    <button type="button" 
                            class="btn btn-secondary"
                            wire:click="cancel">
                        Annuler
                    </button>
                </div>
            </div>
        </div>

        {{-- Liste des employés avec sélection --}}
        <div class="row">
            @forelse($employesDisponibles as $employe)
                <div class="col-md-6 mb-3">
                    <div class="card h-100 {{ in_array($employe->id, $employesSelectionnes) ? 'border-success bg-light' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">
                                        {{ $employe->nom }} {{ $employe->prenom }}
                                    </h6>
                                    @if($employe->matricule)
                                        <small class="text-muted">
                                            <i class="fas fa-id-badge me-1"></i>{{ $employe->matricule }}
                                        </small>
                                    @endif
                                    
                                    {{-- Afficher la consommation si déjà affecté --}}
                                    @php
                                        $employeAffecte = $configuration->employes->find($employe->id);
                                    @endphp
                                    @if($employeAffecte)
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="fas fa-clock me-1"></i>
                                                Consommé : {{ number_format($employeAffecte->pivot->consomme_individuel, 2) }}h
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="employe_{{ $employe->id }}"
                                           wire:click="toggleEmploye({{ $employe->id }})"
                                           @if(in_array($employe->id, $employesSelectionnes)) checked @endif>
                                    <label class="form-check-label" for="employe_{{ $employe->id }}">
                                        @if(in_array($employe->id, $employesSelectionnes))
                                            <span class="badge bg-success">Sélectionné</span>
                                        @else
                                            <span class="badge bg-secondary">Non sélectionné</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">
                            @if($searchEmploye)
                                Aucun employé trouvé pour "{{ $searchEmploye }}"
                            @else
                                Aucun employé disponible
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Résumé des sélections --}}
        @if($nombreEmployesSelectionnes > 0)
            <div class="mt-4 p-3 bg-success bg-opacity-10 border border-success rounded">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>
                    Résumé des affectations
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Employés sélectionnés</small>
                        <strong>{{ $nombreEmployesSelectionnes }} employé(s)</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Quota partagé</small>
                        <strong>{{ number_format($configuration->quota, 2) }} heures</strong>
                    </div>
                </div>
            </div>
        @endif

        {{-- Boutons d'action en bas --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Les employés sélectionnés partageront le quota total de {{ number_format($configuration->quota ?? 0, 2) }} heures
                </small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" 
                        class="btn btn-outline-secondary"
                        wire:click="cancel">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" 
                        class="btn btn-success"
                        wire:click="sauvegarderAffectations"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-save me-2"></i>
                        Sauvegarder ({{ $nombreEmployesSelectionnes }})
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Sauvegarde...
                    </span>
                </button>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Configuration non trouvée.
        </div>
    @endif
</div>