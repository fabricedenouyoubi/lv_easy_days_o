<div>

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Feuilles de temps', 'url' => route('feuille-temps.list')],
        ['label' => 'Détails semaine ' . ($semaine->numero_semaine)]
    ]" />

    {{-- Messages de feedback --}}
    <x-alert-messages />

    <!-- Main content area -->
    <div class="row">
        <!-- Main content - 9 colonnes -->
        <div class="col-12 col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="mdi mdi-clock-time-four-outline me-1 text-primary"></i>
                                Feuille de temps
                            </h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @php $statut = $this->getStatutFormate(); @endphp
                            <span class="badge {{ $statut['class'] }} rounded-pill px-3 py-2">
                                <i class="{{ $statut['icon'] }} align-middle me-1"></i>
                                {{ $statut['text'] }}
                            </span>

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

                    <!-- Détail des lignes de travail -->
                    <div class="card mt-3 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    Détail de la feuille de temps
                                </h6>

                                <div class="d-flex align-items-center gap-2">
                                    <!-- Boutons d'action déplacés dans le header -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('feuille-temps.list') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="mdi mdi-arrow-left me-1"></i>
                                            Retour
                                        </a>
                                        <!-- Actions selon les permissions -->
                                        @if($canEdit)
                                        <a href="{{ route('feuille-temps.edit', ['semaineId' => $semaineId, 'operationId' => $operationId]) }}"
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-square-edit-outline me-1"></i>
                                            Modifier
                                        </a>
                                        @endif

                                        @if($canSubmit)
                                        <button wire:click="toggleSubmitModal" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-send-circle-outline me-1"></i>
                                            Soumettre
                                        </button>
                                        @endif

                                        @if($canRecall)
                                        <button wire:click="toggleRecallModal" class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-backup-restore me-1"></i>
                                            Rappeler
                                        </button>
                                        @endif

                                        @if($canApprove)
                                        <button wire:click="toggleApproveModal" class="btn btn-sm btn-outline-success">
                                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                            Valider
                                        </button>
                                        @endif

                                        @if($canReject)
                                        <button wire:click="toggleRejectModal" class="btn btn-sm btn-outline-danger">
                                            <i class="mdi mdi-close-circle-outline me-1"></i>
                                            Rejeter
                                        </button>
                                        @endif

                                        @if($canReturn)
                                        <button wire:click="toggleReturnModal" class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-reply me-1"></i>
                                            Retourner
                                        </button>
                                        @endif
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code de travail</th>
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
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lignesTravail as $ligne)
                                        <tr class="{{ $ligne['auto_rempli'] ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($ligne['auto_rempli'])
                                                    <i class="mdi mdi-lock text-warning me-2" title="Auto-rempli"></i>
                                                    @endif
                                                    {{ $ligne['code_travail']->libelle }}
                                                </div>
                                            </td>
                                            @foreach($ligne['jours'] as $indexJour => $jour)
                                            <td class="text-center {{ $this->estJourFerie($indexJour) ? 'bg-danger bg-opacity-25' : '' }}">
                                                @if($jour['duree'] > 0)
                                                <span class="badge bg-primary rounded-pill fw-bold">
                                                    {{ $jour['duree'] }}h
                                                </span>
                                                @else
                                                <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            @endforeach
                                            <td class="text-center">
                                                <span class="badge bg-dark rounded-pill">
                                                    {{ $ligne['total'] }}h
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ count($datesSemaine) + 2 }}" class="text-center py-4">
                                                <i class="mdi mdi-clipboard-outline text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mb-0">Aucune ligne de travail enregistrée</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journal du workflow -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="mdi mdi-history me-1 text-primary"></i>
                        Journal de la feuille de temps
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($workflowHistory) > 0)
                    <div class="timeline">
                        @foreach($workflowHistory as $entry)
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center">
                                <span class="badge bg-info me-2">
                                    <i class="mdi mdi-swap-horizontal"></i>
                                </span>
                                <strong>{{ $entry['title'] ?? 'Transition' }}</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">
                                    {{ $entry['date'] ?? '' }} à {{ $entry['time'] ?? '' }}
                                    @if(!empty($entry['user']))
                                    par {{ $entry['user'] }}
                                    @endif
                                </small>
                            </div>
                            @if(!empty($entry['comment']))
                            <div class="col-12">
                                <p class="mb-0" style="white-space: pre-line;">{{ str_replace('Motif:', "\nNote : ", $entry['comment']) }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-comment-text-outline text-muted" style="font-size: 48px;"></i>
                        <h5>Aucune entrée dans le journal</h5>
                        <p class="text-muted">Les changements d'état apparaîtront ici.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - 3 colonnes -->
        <div class="col-12 col-lg-3 mt-4 mt-lg-0">

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

            <!-- Récapitulatif dynamique -->
            <x-table-card title="Résumé des heures" icon="fas fa-clock">
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

                <!-- Heures supplémentaires ajustées -->
                @if($heureSupplementaireAjuste > 0)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Heure Supp. ajusté</span>
                    <span class="badge bg-warning text-dark">
                        {{ number_format($heureSupplementaireAjuste, 2) }}h
                    </span>
                </div>
                @endif

                <!-- Heures supplémentaires à payer -->
                @if($heureSupplementaireAPayer > 0)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Heure Supp. à payer</span>
                    <span class="badge bg-warning text-dark">
                        {{ number_format($heureSupplementaireAPayer, 2) }}h
                    </span>
                </div>
                @endif

                @if(count($totauxrecapitulatif) > 0 || $heureSupplementaireAjuste > 0 || $heureSupplementaireAPayer > 0)
                <hr class="my-3">
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted fw-bold">Total des heures</span>
                    <span class="badge bg-dark px-3 py-2">{{ number_format($totalGeneral, 2) }}h</span>
                </div>

            </x-table-card>


            <!-- MODIFIER la section "Détails Heures Sup." pour inclure les heures manquantes -->

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

    {{-- Modals pour les actions workflow --}}

    {{-- Modal Soumettre --}}
    @if($showSubmitModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Soumettre la feuille de temps</h5>
                    <button type="button" wire:click="toggleSubmitModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir soumettre cette feuille de temps ?</p>
                    <div class="mb-3">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea wire:model="commentaire" class="form-control" rows="3"
                            placeholder="Ajouter un commentaire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="toggleSubmitModal" class="btn btn-secondary">Annuler</button>
                    <button type="button" wire:click="soumettre" class="btn btn-primary">Confirmer la soumission</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Rappeler --}}
    @if($showRecallModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rappeler la feuille de temps</h5>
                    <button type="button" wire:click="toggleRecallModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir rappeler cette feuille de temps pour modification ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif (optionnel)</label>
                        <textarea wire:model="commentaire" class="form-control" rows="3"
                            placeholder="Raison du rappel..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="toggleRecallModal" class="btn btn-secondary">Annuler</button>
                    <button type="button" wire:click="rappeler" class="btn btn-warning">Confirmer le rappel</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Approuver --}}
    @if($showApproveModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Valider la feuille de temps</h5>
                    <button type="button" wire:click="toggleApproveModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir valider cette feuille de temps ?</p>
                    <div class="mb-3">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea wire:model="commentaire" class="form-control" rows="3"
                            placeholder="Commentaire de validation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="toggleApproveModal" class="btn btn-secondary">Annuler</button>
                    <button type="button" wire:click="approuver" class="btn btn-success">Confirmer la validation</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Rejeter --}}
    @if($showRejectModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejeter la feuille de temps</h5>
                    <button type="button" wire:click="toggleRejectModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir rejeter cette feuille de temps ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif de rejet <span class="text-danger">*</span></label>
                        <textarea wire:model="motifRejet" class="form-control @error('motifRejet') is-invalid @enderror"
                            rows="4" placeholder="Expliquez pourquoi vous rejetez cette feuille..." required></textarea>
                        @error('motifRejet')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="toggleRejectModal" class="btn btn-secondary">Annuler</button>
                    <button type="button" wire:click="rejeter" class="btn btn-danger">Confirmer le rejet</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Retourner (Admin) --}}
    @if($showReturnModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Retourner la feuille de temps</h5>
                    <button type="button" wire:click="toggleReturnModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir retourner cette feuille de temps ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif du retour <span class="text-danger">*</span></label>
                        <textarea wire:model="motifRejet" class="form-control @error('motifRejet') is-invalid @enderror"
                            rows="4" placeholder="Expliquez pourquoi vous retournez cette feuille..." required></textarea>
                        @error('motifRejet')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="toggleReturnModal" class="btn btn-secondary">Annuler</button>
                    <button type="button" wire:click="retourner" class="btn btn-warning">Confirmer le retour</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>