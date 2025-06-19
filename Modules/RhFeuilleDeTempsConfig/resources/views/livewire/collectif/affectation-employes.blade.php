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
                        {{ $nombreEmployesSelectionnes }} employé(s)
                    </span>
                </div>
            </div>
        </div>

        {{-- Barre de recherche --}}
        <div class="row mb-3">
            <div class="col-md-12">
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
        </div>

        {{-- Liste simplifiée des employés --}}
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="selectAll">
                        </th>
                        <th>Employé</th>
                        <th>Quota consommé</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employesDisponibles as $employe)
                        @php
                            $employeAffecte = $configuration->employes->find($employe->id);
                            $isSelected = in_array($employe->id, $employesSelectionnes);
                        @endphp
                        <tr class="{{ $isSelected ? 'table-success' : '' }}">
                            <td>
                                <input class="form-check-input {{ $isSelected ? '' : 'bg-white border-secondary' }}" 
                                       type="checkbox" 
                                       id="employe_{{ $employe->id }}"
                                       wire:click="toggleEmploye({{ $employe->id }})"
                                       @if($isSelected) checked @endif>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $employe->nom }} {{ $employe->prenom }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($employeAffecte)
                                    <span class="text-info">
                                        {{ number_format($employeAffecte->pivot->consomme_individuel, 2) }}h
                                    </span>
                                @else
                                    <span class="text-muted">0h</span>
                                @endif
                            </td>
                            <td>
                                @if($isSelected)
                                    <span class="badge bg-success">Sélectionné</span>
                                @else
                                    <span class="badge bg-secondary">Non sélectionné</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">
                                    @if($searchEmploye)
                                        Aucun employé trouvé pour "{{ $searchEmploye }}"
                                    @else
                                        Aucun employé disponible
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Boutons d'action en bas --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Total quota horaire {{ number_format($configuration->quota ?? 0, 2) }} heures
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