<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <div class="row">
        <div class="col-12 col-lg-9">
            {{-- Info demande d'absence --}}
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center gy-2">
                        <div class="col">
                            <h4 class="card-title mb-0 d-flex align-items-center">
                                <i class="fas fa-clock me-2"></i>
                                Demande d'absence
                            </h4>
                        </div>
                        <div class="col-auto">
                            {{-- Bounton retour --}}
                            <x-action-button type="secondary" size="sm" icon="fas fa-arrow-left"
                                text="Retour à la liste" href="{{ route('absence.list') }}" />
                        </div>
                        @if ($demandeAbsence->status == 'En cours' && $demandeAbsence->employe_id == auth()->user()->employe->id)
                            {{-- Bounton Nodifier --}}
                            <div class="col-auto">
                                <x-action-button type="primary" size="sm" icon="fas fa-edit" text="Modifier"
                                    wireClick="toogle_edit_absence_modal" />
                            </div>
                            {{-- Bounton Soumettre --}}
                            <div class="col-auto">
                                <x-action-button type="success" size="sm" icon="fas fa-paper-plane"
                                    text="Soumettre" wireClick="toogle_soumission_modal" />
                            </div>
                        @endif
                        @if ($demandeAbsence->employe->gestionnaire_id == auth()->user()->employe->id)
                            @if ($demandeAbsence->status == 'Soumis')
                                {{-- Bounton Rappeller --}}
                                <div class="col-auto">
                                    <x-action-button type="primary" size="sm" icon="fas fa-undo-alt"
                                        text="Rappeller" wireClick="toogle_rappeler_modal" />
                                </div>
                                {{-- Bounton Valider --}}
                                <div class="col-auto">
                                    <x-action-button type="success" size="sm" icon="fas fa-check-circle"
                                        text="Valider" wireClick="toogle_approve_modal" />
                                </div>
                                {{-- Bounton Rejeter --}}
                                <div class="col-auto">
                                    <x-action-button type="danger" size="sm" icon="fas fa-times-circle"
                                        text="Rejeter" wireClick="toogle_rejeter_modal" />
                                </div>
                            @endif

                            @if ($demandeAbsence->status == 'Validé')
                                {{-- Bounton Retourner --}}
                                <div class="col-auto">
                                    <x-action-button type="warning" size="sm" icon="fas fa-reply" text="Retrourner"
                                        wireClick="toogle_retrouner_modal" />
                                </div>
                                {{-- Bounton Rejeter --}}
                                <div class="col-auto">
                                    <x-action-button type="danger" size="sm" icon="fas fa-times-circle"
                                        text="Rejeter" wireClick="toogle_rejeter_modal" />
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="avatar-wrapper">
                                <div class="avatar-placeholder d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm border border-3 border-white"
                                    style="width: 100px; height: 100px;">
                                    <i class="fa fa-user-circle text-primary" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <strong
                                class="fs-4">{{ $demandeAbsence->employe?->nom . ' ' . $demandeAbsence->employe?->prenom }}</strong>
                        </div>
                    </div>

                    <div class="row gap-4 px-3">
                        <div class="col bg-light p-4 border border-rounded-5">
                            <h6 class="text-muted mb-2">Période d'absence</h6>
                            <div class="d-flex align-items-center gap-4">
                                <i class="fas fa-calendar fs-3"></i>
                                <div class="mt-3">
                                    <p class="mb-1"><strong>Du:</strong>
                                        {{ \Carbon\Carbon::parse($demandeAbsence->date_debut)->format('d/m/Y') }}</p>
                                    <p class="mb-1"><strong>Au:</strong>
                                        {{ \Carbon\Carbon::parse($demandeAbsence->date_fin)->format('d/m/Y') }}</p>
                                    <p class="badge bg-info">{{ $nombreJourAbsence }} jours</p>
                                </div>
                            </div>
                        </div>
                        <div class="col bg-light p-4 border border-rounded-5">
                            <h6 class="text-muted mb-2">Type d'absence</h6>
                            <div class="d-flex align-items-center gap-4">
                                <i class="fas fa-tags fs-3"></i>
                                <div class="mt-3">
                                    <p class="mb-1 fs-5"><strong>{{ $demandeAbsence->CodeTravail?->libelle }}</strong>
                                    </p>
                                    <p class="badge bg-info">{{ $demandeAbsence->CodeTravail?->libelle }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Journal dde demande d'absence --}}
            <x-table-card title="Journal de la demande d'absence" icon="fas fa-history">
                @foreach ($logs as $log)
                    <div class="row mb-3">
                        <div class="col-12 d-flex align-items-center">
                            <span class="badge bg-info"><i class="fas fa-exchange-alt"></i></span> &nbsp;
                            {{ $log['to_state'] }}
                        </div>
                        <div class="col-12">
                            <p class="mb-1"> Date: {{ $log['date'] }} </p>
                            <p class="mb-1"> Heure: {{ $log['time'] }} </p>
                        </div>
                        <div class="col-12">
                            <span>{{ $log['title'] }}</span>.
                            <strong>{{ $log['comment'] }}</strong>.
                        </div>
                    </div>
                @endforeach
            </x-table-card>
        </div>

        <div class="col col-lg-3">
            {{-- Statut de la demande --}}
            <x-table-card title="Statut actuel" icon="fas fa-battery-half">
                {{-- statut en cours --}}
                @if ($demandeAbsence->status == 'En cours')
                    <div class="row">
                        <div class="d-flex justify-content-center mb-2">
                            <i class="fas fa-spinner fs-1"></i>
                        </div>
                        <div class="text-center">
                            <strong class="fs-5">En cours</strong>
                            <p>Votre demande d'absence est en cours de traitement.</p>
                        </div>
                        <div class="mt-1">
                            <h6 class="mb-2">Étapes suivantes</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <div class="text-justify">
                                        <span class="mb-1">- Votre demande est en cours de traitement</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
                {{-- Statut soumis --}}
                @if ($demandeAbsence->status == 'Soumis')
                    <div class="row">
                        <div class="d-flex justify-content-center mb-2">
                            <i class="fas fa-stopwatch fs-1"></i>
                        </div>
                        <div class="text-center">
                            <strong class="fs-5">En attente d'approbation</strong>
                            <p>Votre demande d'absence est en cours d'examen.</p>
                        </div>
                        <div class="mt-1">
                            <h6 class="mb-2">Étapes suivantes</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <div class="text-justify w-100">
                                        <p class="mb-1">- Votre responsable va examiner votre demande</p>
                                        <p>- Vous serez notifié de l'approbation ou du rejet</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Statut approuvée --}}
                @if ($demandeAbsence->status == 'Validé')
                    <div class="row">
                        <div class="d-flex justify-content-center mb-2">
                            <i class="fas fa-check-circle fs-1"></i>
                        </div>
                        <div class="text-center">
                            <strong class="fs-5">Demande approuvée</strong>
                            <p>Votre demande d'absence a été enregistrée.</p>
                        </div>
                        <div class="mt-1">
                            <h6 class="mb-2">Étapes suivantes</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <div class="text-justify w-100">
                                        <p class="mb-1">- Absence enregistrée dans votre calendrier</p>
                                        <p>- Traitement administratif en cours</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Statut rejetée --}}
                @if ($demandeAbsence->status == 'Rejeté')
                    <div class="row">
                        <div class="d-flex justify-content-center mb-2">
                            <i class="fas fa-times-circle fs-1"></i>
                        </div>
                        <div class="text-center">
                            <strong class="fs-5">Demande Rejetée</strong>
                            <p>Votre demande d'absence a été rejetée.</p>
                        </div>
                    </div>
                @endif

            </x-table-card>

            {{-- Banque de temps --}}
            <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Banque de temps</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        0h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Vacances</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        0h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Heure CSN</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        0h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-3">
                    <span class="text-muted">Total des heures en banque</span>
                    <span class="badge bg-dark px-3 py-2 rounded-pill">
                        0h
                    </span>
                </div>
            </x-table-card>
        </div>
    </div>

    {{-- Modifier la demande d'absence --}}
    @if ($showEditAbsenceModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Mofidier la demande</h5>
                        <x-action-button type="close" wire-click="toogle_edit_absence_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-form :demande_absence_id="$demandeAbsenceId" />
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- Valider la soumission de la demande d'absence --}}
    @if ($showApprouverModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Valider la demande</h5>
                        <x-action-button type="close" wire-click="toogle_approve_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir valider cette demande d'absence ?</p>
                        <div class="modal-footer">
                            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                                wireClick="toogle_approve_modal" text="Annuler" />
                            <x-action-button type="success" icon="fas fa-check-circle me-2" size="md"
                                text="confirmer la validation" wireClick="approuverDemandeAbsence" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Valider la soumission de la demande d'absence --}}
    @if ($showSoumissionModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Soumettre la demande</h5>
                        <x-action-button type="close" wire-click="toogle_soumission_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir soumettre cette demande d'absence ?</p>
                        <div class="modal-footer">
                            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                                wireClick="toogle_soumission_modal" text="Annuler" />
                            <x-action-button type="success" icon="fas fa-paper-plane me-2" size="md"
                                text="confirmer la soumission" wireClick="soumettreDemandeAbsence" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Rappeler la demande d'absence --}}
    @if ($showRappelerModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Rappeller la demande</h5>
                        <x-action-button type="close" wire-click="toogle_rappeler_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir rappeler cette demande d'absence ?</p>
                        <div class="modal-footer">
                            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                                wireClick="toogle_rappeler_modal" text="Annuler" />
                            <x-action-button type="success" icon="fas fa-undo-alt me-2" size="md"
                                text="Confirmer le rappel" wireClick="rapelleDemandeAbsence" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Retourner la demande d'absence --}}
    @if ($showRetournerModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Retourner la demande</h5>
                        <x-action-button type="close" wire-click="toogle_retrouner_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir retourner cette demande d'absence ?</p>
                        <div class="modal-footer">
                            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                                wireClick="toogle_retrouner_modal" text="Annuler" />
                            <x-action-button type="success" icon="fas fa-reply me-2" size="md"
                                text="Confirmer le retour" wireClick="retournerDemandeAbsence" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- Rejeter la demande d'absence --}}
    @if ($showRejeterModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Rejeter la demande</h5>
                        <x-action-button type="close" wire-click="toogle_rejeter_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir rejeter cette demande d'absence ?</p>
                        <div class="modal-footer">
                            <x-action-button type="secondary" icon="fas fa-times me-2" size="md"
                                wireClick="toogle_rejeter_modal" text="Annuler" />
                            <x-action-button type="success" icon="fas fa-times-circle me-2" size="md"
                                text="Confirmer le rejet" wireClick="rejeterDemandeAbsence" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
