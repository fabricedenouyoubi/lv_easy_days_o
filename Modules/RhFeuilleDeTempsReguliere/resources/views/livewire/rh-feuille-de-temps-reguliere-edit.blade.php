<div>
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Feuilles de temps', 'url' => route('feuille-temps.list')],
        ['label' => 'Détails semaine ' . ($semaine->numero_semaine)]
    ]" />

    {{-- Messages de feedback --}}
    <x-alert-messages />

    <form wire:submit.prevent="enregistrer" class="h-100">
        <div class="container-fluid py-3">

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
                                <div class="d-flex align-items-center gap-2">
                                    @if($operation->workflow_state)
                                    @php
                                    $statusConfig = [
                                    'brouillon' => ['class' => 'bg-warning text-dark', 'icon' => 'fas fa-pencil-alt'],
                                    'en_cours' => ['class' => 'bg-info text-dark', 'icon' => 'fas fa-hourglass-half'],
                                    'soumis' => ['class' => 'bg-primary', 'icon' => 'fas fa-paper-plane'],
                                    'valide' => ['class' => 'bg-success', 'icon' => 'fas fa-check-circle'],
                                    ];
                                    $status = $statusConfig[$operation->workflow_state] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question-circle'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }} rounded-pill px-3 py-2">
                                        <i class="{{ $status['icon'] }} align-middle me-1"></i>
                                        {{ ucfirst($operation->workflow_state) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Informations employé -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                                            <span class="font-size-24 text-primary fw-bold">
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
                                <div class="col-12 mb-3">
                                    <div class="card h-100 border-0 bg-primary-subtle">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                                                    <i class="mdi mdi-calendar-week text-white" style="font-size: 24px;"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">Semaine {{ $semaine->numero_semaine }}</h5>
                                                    <p class="mb-0 fw-bold small">
                                                        Période Du {{ \Carbon\Carbon::parse($semaine->debut)->format('d/m/Y') }}
                                                        au {{ \Carbon\Carbon::parse($semaine->fin)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lignes de travail -->
                            {{-- Vérifier si l'employé peut modifier (propriétaire + état modifiable) --}}
                            @if ($operation->employe_id === auth()->user()->employe?->id && in_array($operation->workflow_state, ['brouillon', 'en_cours', 'rejete']))
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                                            Lignes de travail
                                        </h6>

                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Boutons d'action avec permissions -->
                                            <div class="d-flex gap-2">
                                                <x-action-button 
                                                    type="secondary" 
                                                    icon="mdi mdi-arrow-left" 
                                                    size="sm"
                                                    text="Retour à la liste" 
                                                    href="{{ route('feuille-temps.list') }}" />
                                                
                                                {{-- Enregistrer : toujours disponible en mode édition --}}
                                                <x-action-button 
                                                    type="primary" 
                                                    icon="mdi mdi-content-save-outline" 
                                                    size="sm"
                                                    text="Enregistrer" 
                                                    typeButton="submit"
                                                    loading="true"
                                                    loading-target="enregistrer" />
                                                
                                                {{-- Soumettre : états brouillon, en_cours, rejete --}}
                                                @if (in_array($operation->workflow_state, ['brouillon', 'en_cours', 'rejete']))
                                                    <x-action-button 
                                                        type="success" 
                                                        icon="mdi mdi-send-outline" 
                                                        size="sm"
                                                        text="Soumettre" 
                                                        wireClick="soumettre"
                                                        loading="true"
                                                        loading-target="soumettre" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="min-width: 200px;">Code de travail</th>

                                                    @foreach($datesSemaine as $index => $dateInfo)
                                                    <th class="text-center {{ $this->estJourFerie($index) ? 'bg-danger bg-opacity-25' : '' }}">
                                                        {{ $dateInfo['jour_nom'] }}
                                                        @if($this->estJourFerie($index))
                                                        <i class="mdi mdi-calendar-remove text-danger ms-1" title="Jour férié"></i>
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $dateInfo['date']->format('d/m') }}
                                                        </small>
                                                    </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lignesTravail as $ligneIndex => $ligne)
                                                <tr class="{{ $ligne['auto_rempli'] ? 'table-warning' : '' }}">
                                                    <!-- Code de travail grisé -->
                                                    <td class="bg-light">
                                                        <div class="d-flex align-items-center">
                                                            @if($ligne['auto_rempli'])
                                                            <i class="mdi mdi-lock text-warning me-2" title="Auto-rempli"></i>
                                                            @endif
                                                            <span class="fw-medium">{{ $ligne['code_travail']->libelle }}</span>
                                                        </div>
                                                    </td>

                                                    @for($jour = 0; $jour <= 6; $jour++)
                                                        <td class="text-center {{ $datesSemaine[$jour]['is_dimanche'] ? 'bg-warning bg-opacity-10' : '' }} {{ $this->estJourFerie($jour) ? 'bg-danger bg-opacity-25' : '' }}">
                                                        @if($this->peutModifierLigne($ligneIndex))
                                                        <input type="text"
                                                            wire:model.lazy="lignesTravail.{{ $ligneIndex }}.duree_{{ $jour }}"
                                                            class="form-control form-control-sm text-center"
                                                            placeholder="00.00"
                                                            maxlength="5"
                                                            style="width: 80px; margin: 0 auto; font-family: 'Courier New', monospace;"
                                                            title="Format: HH.MM ou HH,MM (ex: 01.30, 00.15, 07.45)"
                                                            pattern="^\d{1,2}[.,]\d{2}$">
                                                        @else
                                                        <span class="badge bg-primary">
                                                            {{ $ligne["duree_{$jour}"] }}h
                                                        </span>
                                                        @endif
                                                        </td>
                                                        @endfor
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
                                <x-action-button 
                                    type="primary" 
                                    icon="mdi mdi-eye" 
                                    size="sm"
                                    text="Voir les détails" 
                                    href="{{ route('feuille-temps.show', ['semaineId' => $semaineId, 'operationId' => $operationId]) }}" 
                                    class="ms-2" />
                            </div>

                            <!-- Affichage en lecture seule -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                                            Lignes de travail (Lecture seule)
                                        </h6>
                                        <div class="d-flex gap-2">
                                            <x-action-button 
                                                type="secondary" 
                                                icon="mdi mdi-arrow-left" 
                                                size="sm"
                                                text="Retour à la liste" 
                                                href="{{ route('feuille-temps.list') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="min-width: 200px;">Code de travail</th>
                                                    @foreach($datesSemaine as $index => $dateInfo)
                                                    <th class="text-center {{ $dateInfo['is_dimanche'] ? 'bg-warning bg-opacity-25' : '' }}"
                                                        style="min-width: 120px;">
                                                        {{ $dateInfo['format'] }}
                                                    </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lignesTravail as $ligneIndex => $ligne)
                                                @php
                                                $hasHours = false;
                                                for($j = 0; $j <= 6; $j++) {
                                                    if(floatval($ligne["duree_{$j}"] ?? 0) > 0) {
                                                    $hasHours = true;
                                                    break;
                                                    }
                                                    }
                                                    @endphp

                                                    @if($hasHours)
                                                    <tr class="{{ $ligne['auto_rempli'] ? 'table-warning' : '' }}">
                                                        <td class="bg-light">
                                                            <div class="d-flex align-items-center">
                                                                @if($ligne['auto_rempli'])
                                                                <i class="mdi mdi-lock text-warning me-2" title="Auto-rempli"></i>
                                                                @endif
                                                                <span class="fw-medium">{{ $ligne['code_travail']->libelle }}</span>
                                                            </div>
                                                        </td>

                                                        @for($jour = 0; $jour <= 6; $jour++)
                                                            <td class="text-center {{ $datesSemaine[$jour]['is_dimanche'] ? 'bg-warning bg-opacity-10' : '' }} {{ $this->estJourFerie($jour) ? 'bg-danger bg-opacity-25' : '' }}">
                                                            @if(floatval($ligne["duree_{$jour}"] ?? 0) > 0)
                                                            <span class="badge bg-primary">{{ $ligne["duree_{$jour}"] }}h</span>
                                                            @else
                                                            <span class="text-muted">-</span>
                                                            @endif
                                                            </td>
                                                            @endfor
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale (3/12) -->
                <div class="col-xxl-3">

                    <!-- Banque de temps -->
                    <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
                        @if(count($banqueDeTemps) > 0)
                        @foreach($banqueDeTemps as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">{{ $item['libelle'] }}</span>
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                {{ number_format($item['valeur'], 0) }}h
                            </span>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="text-muted">Total en banque</span>
                            <span class="badge bg-dark px-3 py-2 rounded-pill">
                                {{ number_format($this->totalBanqueTemps, 0) }}h
                            </span>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="fas fa-piggy-bank text-muted mb-2" style="font-size: 24px;"></i>
                            <p class="text-muted small mb-0">Aucune banque de temps configurée</p>
                        </div>
                        @endif
                    </x-table-card>

                    <!-- Récapitulatif -->
                    <x-table-card title="Récapitulatif" icon="mdi mdi-calculator-variant-outline">
                        @if(count($totauxrecapitulatif) > 0)
                        @foreach($totauxrecapitulatif as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">{{ $item['code_travail']->libelle }}</span>
                            <span class="badge bg-success">
                                {{ number_format($item['total_heures'], 2) }}h
                            </span>
                        </div>
                        @endforeach

                        <hr class="my-3">
                        @endif

                        <!-- Heures supplémentaires ajustées (LECTURE SEULE) -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Heure Supp. ajustées</span>
                            <span class="badge bg-info text-dark">
                                {{ $heureSupplementaireAjuste }}h
                            </span>
                        </div>

                        <!-- Heures supplémentaires à payer (MODIFIABLE selon permissions) -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Heure Supp. à payer</span>
                            {{-- Modifiable seulement si propriétaire + état modifiable --}}
                            @if ($operation->employe_id === auth()->user()->employe?->id && in_array($operation->workflow_state, ['brouillon', 'en_cours', 'rejete']))
                            <input type="text"
                                wire:model.lazy="heureSupplementaireAPayer"
                                class="form-control form-control-sm text-center"
                                placeholder="00.00"
                                maxlength="5"
                                max="{{ $heureSupplementaireAjuste }}"
                                style="width: 70px; font-family: 'Courier New', monospace;"
                                title="Format: HH.MM ou HH,MM (Max: {{ $heureSupplementaireAjuste }}h)">
                            @else
                            <span class="badge bg-danger">
                                {{ $heureSupplementaireAPayer }}h
                            </span>
                            @endif
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Total des heures</span>
                            <span class="badge bg-dark px-3 py-2">{{ number_format($totalGeneral, 2) }}h</span>
                        </div>
                    </x-table-card>

                    <!-- Détails Heures Sup. -->
                    <x-table-card title="Détails Heures Sup." icon="mdi mdi-clock-plus-outline">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Heures définies</small>
                                <span class="badge bg-primary">{{ number_format($heuresDefiniesEmploye, 0) }}h</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Heures travaillées</small>
                                <span class="badge bg-success">{{ number_format($heuresTravaillees, 2) }}h</span>
                            </div>
                        </div>

                        @php
                        $heuresManquantes = $debugCalculs['heures_manquantes'] ?? 0;
                        $differenceHebdomadaire = $debugCalculs['difference_hebdomadaire'] ?? 0;
                        @endphp

                        <!-- CAS 1: Heures manquantes -->
                        @if($heuresManquantes > 0)
                        <div class="alert alert-warning alert-sm p-2 mb-3">
                            <i class="mdi mdi-alert-circle-outline me-1"></i>
                            <small>
                                <strong>Heures manquantes :</strong> {{ number_format($heuresManquantes, 2) }}h
                            </small>
                        </div>
                        @endif

                        <!-- CAS 2: Heures supplémentaires -->
                        @if($heuresSupNormales > 0)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Heures sup. normales</span>
                            <span class="badge bg-warning text-dark">{{ number_format($heuresSupNormales, 2) }}h</span>
                        </div>
                        @endif

                        @if($heuresSupMajorees > 0)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Heures sup. majorées</span>
                            <span class="badge bg-danger">{{ number_format($heuresSupMajorees, 2) }}h</span>
                        </div>
                        @endif

                        @if($totalHeuresSupAjustees > 0)
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold small">Total ajustées</span>
                            <span class="badge bg-info">{{ number_format($totalHeuresSupAjustees, 2) }}h</span>
                        </div>
                        @endif

                        <!-- Vers banque temps (peut être positif ou négatif) -->
                        @if($versBanqueTemps != 0)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Vers banque temps</span>
                            <span class="badge {{ $versBanqueTemps > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $versBanqueTemps > 0 ? '+' : '' }}{{ number_format($versBanqueTemps, 2) }}h
                            </span>
                        </div>
                        @endif

                        <!-- Affichage différencié selon le cas -->
                        @if($totalHeuresSupAjustees == 0 && $heuresManquantes == 0)
                        <div class="text-center py-2">
                            <i class="mdi mdi-clock-check text-success mb-1" style="font-size: 20px;"></i>
                            <p class="text-muted small mb-0">Heures exactes</p>
                        </div>
                        @elseif($totalHeuresSupAjustees == 0)
                        <div class="text-center py-2">
                            <i class="mdi mdi-clock-check text-muted mb-1" style="font-size: 20px;"></i>
                            <p class="text-muted small mb-0">Aucune heure supplémentaire</p>
                        </div>
                        @endif
                    </x-table-card>

                </div>
            </div>
    </form>

    {{-- Script JavaScript pour la gestion des champs de saisie (seulement en mode édition) --}}
    @if ($operation->employe_id === auth()->user()->employe?->id && in_array($operation->workflow_state, ['brouillon', 'en_cours', 'rejete']))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des champs de saisie d'heures
            const timeInputs = document.querySelectorAll('input[wire\\:model\\.lazy*="duree_"]');

            timeInputs.forEach(input => {
                // Formater la valeur lors du focus (sélectionner tout le texte)
                input.addEventListener('focus', function() {
                    this.select();
                });

                // Validation en temps réel pendant la saisie
                input.addEventListener('input', function() {
                    let value = this.value;

                    // Permettre seulement les chiffres, points et virgules
                    value = value.replace(/[^0-9.,]/g, '');

                    // Remplacer les virgules par des points
                    value = value.replace(',', '.');

                    // Limiter à un seul point
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }

                    // Limiter la longueur
                    if (value.length > 5) {
                        value = value.substring(0, 5);
                    }

                    this.value = value;
                });

                // Validation et formatage lors de la perte de focus
                input.addEventListener('blur', function() {
                    let value = this.value.trim();

                    if (value === '' || value === '0' || value === '00' || value === '00.00' || value === '00,00') {
                        this.value = '00.00';
                        return;
                    }

                    // Remplacer la virgule par un point
                    value = value.replace(',', '.');

                    // Vérifier si c'est un nombre valide
                    if (isNaN(parseFloat(value))) {
                        this.value = '00.00';
                        return;
                    }

                    let numValue = parseFloat(value);

                    // Limiter à 12 heures maximum
                    /* if (numValue > 12) {
                        numValue = 12;
                    } */

                    // Formater avec 2 décimales et zéros de tête
                    this.value = numValue.toFixed(2).padStart(5, '0');
                });
            });
        });
    </script>
    @endif
</div>