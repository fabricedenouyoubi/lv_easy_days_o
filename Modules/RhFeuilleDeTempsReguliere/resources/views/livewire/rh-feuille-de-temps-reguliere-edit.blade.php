<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <form wire:submit.prevent="enregistrer" class="h-100">
        <div class="container-fluid py-3">
            <!-- Breadcrumb -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('feuille-temps.list') }}">Feuilles de temps</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Semaine {{ $semaine->numero_semaine }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row g-4">
                <!-- Colonne principale (9/12) -->
                <div class="col-xxl-9">
                    <!-- Carte principale -->
                    <div class="card bg-white shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="mdi mdi-clock-time-four-outline text-primary me-2"></i>
                                    Feuille de temps
                                </h5>
                                @if($operation->workflow_state)
                                    @php
                                        $statusConfig = [
                                            'brouillon' => ['class' => 'bg-warning text-dark', 'icon' => 'mdi-pencil-outline'],
                                            'en_cours' => ['class' => 'bg-info text-dark', 'icon' => 'mdi-hourglass-half'],
                                            'soumis' => ['class' => 'bg-primary', 'icon' => 'mdi-send'],
                                            'valide' => ['class' => 'bg-success', 'icon' => 'mdi-check-circle'],
                                        ];
                                        $status = $statusConfig[$operation->workflow_state] ?? ['class' => 'bg-secondary', 'icon' => 'mdi-help'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }} rounded-pill px-3 py-2">
                                        <i class="{{ $status['icon'] }} align-middle me-1"></i>
                                        {{ ucfirst($operation->workflow_state) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Informations employé -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-lg bg-light rounded-circle text-center me-3">
                                            <span class="font-size-24 text-primary">
                                                {{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ $employe->prenom }} {{ $employe->nom }}</h4>
                                            <p class="text-muted small mb-0">
                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                35h par semaine
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations période -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-0 bg-primary-subtle">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar-md bg-primary rounded-circle text-center">
                                                    <i class="mdi mdi-calendar-week text-white" style="font-size: 24px; line-height: 56px; margin-left: 16px;"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h5 class="mb-1">{{ $semaine->numero_semaine }}</h5>
                                                    <p class="mb-0 text-muted">Semaine</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8 mb-3">
                                    <div class="card h-100 border-0 bg-success-subtle">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar-md bg-success rounded-circle text-center">
                                                    <i class="mdi mdi-calendar-range text-white" style="font-size: 24px; line-height: 56px; margin-left: 16px;"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h5 class="mb-1">
                                                        Du {{ \Carbon\Carbon::parse($semaine->debut)->format('d/m/Y') }}
                                                        au {{ \Carbon\Carbon::parse($semaine->fin)->format('d/m/Y') }}
                                                    </h5>
                                                    <p class="mb-0 text-muted">Période</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lignes de travail -->
                            @if($this->peutModifier())
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                                                Lignes de travail
                                            </h6>
                                            <button type="button" wire:click="ajouterLigneTravail" 
                                                    class="btn btn-sm btn-outline-success">
                                                <i class="mdi mdi-plus me-1"></i>
                                                Ajouter une ligne
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="min-width: 200px;">Code de travail</th>
                                                        @foreach($joursLabels as $index => $jour)
                                                            <th class="text-center" style="min-width: 80px;">
                                                                {{ $jour }}
                                                                <br>
                                                                <small class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($semaine->debut)->addDays($index)->format('d/m') }}
                                                                </small>
                                                            </th>
                                                        @endforeach
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($lignesTravail as $ligneIndex => $ligne)
                                                        <tr class="{{ $ligne['auto_rempli'] ? 'table-warning' : '' }}">
                                                            <td>
                                                                @if($this->peutModifierLigne($ligneIndex))
                                                                    <select wire:model="lignesTravail.{{ $ligneIndex }}.codes_travail_id" 
                                                                            class="form-select form-select-sm">
                                                                        <option value="">Sélectionner un code</option>
                                                                        @foreach($this->codesTravauxDisponibles as $categorie => $codes)
                                                                            <optgroup label="{{ $categorie }}">
                                                                                @foreach($codes as $code)
                                                                                    <option value="{{ $code->id }}">{{ $code->libelle }}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                @else
                                                                    <span class="badge bg-warning">
                                                                        <i class="mdi mdi-lock me-1"></i>
                                                                        Auto-rempli
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            
                                                            @for($jour = 0; $jour <= 6; $jour++)
                                                                <td class="text-center">
                                                                    @if($this->peutModifierLigne($ligneIndex))
                                                                        <input type="number" 
                                                                               wire:model="lignesTravail.{{ $ligneIndex }}.duree_{{ $jour }}"
                                                                               class="form-control form-control-sm text-center"
                                                                               min="0" max="12" step="0.25"
                                                                               style="width: 70px; margin: 0 auto;">
                                                                    @else
                                                                        <span class="badge bg-primary">
                                                                            {{ $ligne["duree_{$jour}"] ?? 0 }}h
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            @endfor
                                                            
                                                            <td class="text-center">
                                                                @if($this->peutModifierLigne($ligneIndex))
                                                                    <button type="button" 
                                                                            wire:click="supprimerLigneTravail({{ $ligneIndex }})"
                                                                            class="btn btn-outline-danger btn-sm">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                @else
                                                                    <i class="mdi mdi-lock text-muted"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Vue lecture seule -->
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    Cette feuille de temps est en lecture seule (statut: {{ $operation->workflow_state }}).
                                </div>
                            @endif
                        </div>

                        <!-- Pied de page avec boutons d'action -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('feuille-temps.list') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i>
                                        Retour à la liste
                                    </a>
                                </div>
                                @if($this->peutModifier())
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="mdi mdi-content-save-outline me-1"></i>
                                            Enregistrer
                                        </button>
                                        <button type="button" wire:click="soumettre" class="btn btn-primary">
                                            <i class="mdi mdi-send-outline me-1"></i>
                                            Soumettre
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale (3/12) -->
                <div class="col-xxl-3">
                    <!-- Totaux calculés -->
                    <x-table-card title="Récapitulatif" icon="mdi mdi-calculator-variant-outline">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Formation</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_formation'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">CSN</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_csn'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Caisse</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_caisse'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Congé Mobile</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_conge_mobile'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Déplacement</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_deplacement'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Régulier</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_regulier'] }}h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Heures Supp.</span>
                            <span class="badge bg-primary">{{ $totaux['total_heure_supp'] }}h</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Total des heures</span>
                            <span class="badge bg-dark px-3 py-2">{{ $totaux['total_heure'] }}h</span>
                        </div>
                    </x-table-card>

                    <!-- Banque de temps -->
                    <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Vacances</span>
                            <span class="badge bg-success px-3 py-2 rounded-pill">5h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Banque de temps</span>
                            <span class="badge bg-success px-3 py-2 rounded-pill">10h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Heure CSN</span>
                            <span class="badge bg-success px-3 py-2 rounded-pill">45h</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="text-muted">Total en banque</span>
                            <span class="badge bg-dark px-3 py-2 rounded-pill">60h</span>
                        </div>
                    </x-table-card>
                </div>
            </div>
        </div>
    </form>
</div>
