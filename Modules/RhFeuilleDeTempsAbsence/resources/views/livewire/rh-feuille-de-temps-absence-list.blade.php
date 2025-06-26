<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <div class="row">
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 d-flex align-items-center">
                                <i class="fas fa-clock me-2"></i>
                                Mes demandes d'absence
                            </h4>
                        </div>
                        <div class="col-auto">
                            <x-action-button type="primary" size="sm" icon="fas fa-plus" text="Nouvelle demande"
                                wireClick="toogle_add_absence_modal" />
                        </div>
                        @if (auth()->user()->hasRole('ADMIN'))
                            <div class="col-auto">
                                <x-action-button type="primary" size="sm" icon="fas fa-plus"
                                    text="Demande pour un autre employé" wireClick="toogle_add_employe_absence_modal" />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="w-100">
                        <ul class="nav nav-tabs w-100" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#venir" role="tab"
                                    aria-selected="true">
                                    <span class="d-none d-sm-block">
                                        <i class="far fa-calendar-plus"></i>
                                        A venir
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#passee" role="tab"
                                    aria-selected="false" tabindex="-1">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">
                                        <i class="far fa-calendar-check"></i>
                                        Passée
                                    </span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active show" id="venir" role="tabpanel">
                                {{-- Contenu du tableau --}}
                                <div class="table-responsive">
                                    <table class="table table-nowrap align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employé(e)</th>
                                                <th>Type d'absence</th>
                                                <th>Date de début de l'absence</th>
                                                <th>Date de début de l'absence</th>
                                                <th>Statut de la demande</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($demande_absences as $demande_absence)
                                                <tr>
                                                    <td>
                                                        <span>{{ $demande_absence->employe?->nom . ' ' . $demande_absence->employe?->prenom }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->codeTravail?->libelle }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->date_debut }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->date_fin }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $demande_absence->statut }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            {{-- Boutons avec composant --}}
                                                            <x-action-button type="outline-info" icon="fas fa-eye"
                                                                tooltip="Consulter la demande"
                                                                href="{{ route('absence.show', $demande_absence->id) }}" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="far fa-calendar fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">Aucune Demande trouvée</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="mt-3">
                                    {{-- {{ $employes->links() }} --}}
                                </div>
                            </div>
                            <div class="tab-pane" id="passee" role="tabpanel">
                                {{-- Contenu du tableau --}}
                                <div class="table-responsive">
                                    <table class="table table-nowrap align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employé(e)</th>
                                                <th>Type d'absence</th>
                                                <th>Date de début de l'absence</th>
                                                <th>Date de début de l'absence</th>
                                                <th>Statut de la demande</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($demande_absences_close as $demande_absence)
                                                <tr>
                                                    <td>
                                                        <span>{{ $demande_absence->employe?->nom . ' ' . $demande_absence->employe?->prenom }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->codeTravail?->libelle }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->date_debut }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ $demande_absence->date_fin }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $demande_absence->statut }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="far fa-calendar fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">Aucune Demande trouvée</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="mt-3">
                                    {{-- {{ $employes->links() }} --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <span class="badge bg-info">{{ $nbrDemandeEnAttente }} en attente</span>
                        <span class="badge bg-success">{{ $nbrDemandeApprouve }} approuvée(s)</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12 col-lg-3">
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

    {{-- Formulaire d'ajout d'une demande --}}
    @if ($showAddAbsenceModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Nouvelle demande</h5>
                        <x-action-button type="close" wire-click="toogle_add_absence_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-form :demande_absence_id="$demandeAbsenceId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Formulaire d'ajout d'une demande pour un autre employe --}}
    @if ($showAddEmployeAbsenceModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Nouvelle demande pour un autre employé
                        </h5>
                        <x-action-button type="close" wire-click="toogle_add_employe_absence_modal"
                            aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-admin-form />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
