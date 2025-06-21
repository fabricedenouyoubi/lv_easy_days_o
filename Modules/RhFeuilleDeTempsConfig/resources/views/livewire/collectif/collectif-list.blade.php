{{-- DIV RACINE UNIQUE --}}
<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Header principal --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="fas fa-users-cog me-2"></i>
                Configuration collective - {{ $codeTravail->libelle }}
            </h4>
        </div>
    </div>

    {{-- Layout principal avec deux colonnes --}}
    <div class="row">
        {{-- Colonne de gauche : Liste des employés --}}
        <div class="col-lg-8">
            @if ($configuration)
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users me-2"></i>
                                    Employés affectés ({{ $employesAffectes->count() }})
                                </h5>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-2">
                                    <x-action-button type="outline-secondary" icon="fas fa-arrow-left me-2" text="Retour"
                                        href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}" />
                                    <x-action-button type="primary" icon="fas fa-user-plus me-2" 
                                        text="Affecter employé" wireClick="showAffectationModal" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($employesAffectes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employé</th>
                                            <th>Quota horaire consommé</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employesAffectes as $employe)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $employe->nom }} {{ $employe->prenom }}</strong>
                                                        @if ($employe->matricule)
                                                            <br><small class="text-muted">{{ $employe->matricule }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ number_format($employe->pivot->consomme_individuel, 2) }}h
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucun employé affecté</p>
                                <small class="text-muted">Utilisez le panneau de droite pour commencer</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informations récapitulatives sous le tableau --}}
                <div class="alert alert-info mt-3">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="d-flex flex-column">
                                <strong class="fs-4 text-primary">{{ number_format($configuration->quota, 2) }}h</strong>
                                <small class="text-muted">Quota total</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column">
                                <strong class="fs-4 text-warning">{{ number_format($quotaConsomme, 2) }}h</strong>
                                <small class="text-muted">Quota consommé</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column">
                                <strong class="fs-4 text-success">{{ number_format($quotaRestant, 2) }}h</strong>
                                <small class="text-muted">Quota restant</small>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- État initial quand aucune configuration n'existe --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-cogs fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">Configuration collective non définie</h5>
                        <p class="text-muted mb-4">
                            Commencez par définir le quota horaire total dans le panneau de droite
                        </p>
                        <i class="fas fa-arrow-right fa-2x text-primary"></i>
                    </div>
                </div>
            @endif
        </div>

        {{-- Colonne de droite : Configuration du quota --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Quota horaire total
                    </h5>
                </div>
                <div class="card-body">
                    @if (!$anneeBudgetaireActive)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Aucune année financière active. Impossible de créer une configuration.
                        </div>
                    @else
                        <form wire:submit.prevent="saveQuota">
                            <div class="mb-3">
                                <label for="quotaTotal" class="form-label">
                                    Quota horaire total <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" id="quotaTotal" 
                                        class="form-control @error('quotaTotal') is-invalid @enderror"
                                        wire:model="quotaTotal" 
                                        step="0.01" 
                                        min="0" 
                                        max="9999.99" 
                                        placeholder="Ex: 40.00"
                                        @if(!$isEditing && $configuration) readonly @endif>
                                    <span class="input-group-text">heures</span>
                                    @error('quotaTotal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Total des heures disponibles pour cette configuration
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                @if ($isEditing)
                                    <x-action-button type="success" icon="fas fa-save me-2" 
                                        text="Enregistrer" typeButton="submit" />
                                    <x-action-button type="secondary" icon="fas fa-times me-2" 
                                        text="Annuler" wireClick="cancelEditing" />
                                @else
                                    @if ($configuration)
                                        <x-action-button type="primary" icon="fas fa-edit me-2" 
                                            text="Modifier quota" wireClick="enableEditing" />
                                    @else
                                        <x-action-button type="success" icon="fas fa-save me-2" 
                                            text="Créer configuration" typeButton="submit" />
                                    @endif
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Affectation Employés --}}
    @if ($showAffectation && $configuration)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>
                            Affecter des employés
                        </h5>
                        <x-action-button type="close btn-close-primary" wireClick='closeAffectationModal' />
                    </div>
                    <div class="modal-body">
                        <livewire:rh-comportement::affectation-employes :configurationId="$configuration->id" :key="$configuration->id" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>