<div>
    <div class="row">
        <div class="col-12 col-lg-9">
            <x-table-card title="Mes demandes d'absence" icon="fas fa-clock" button-text="Nouvelle demande"
                button-action="toogle_add_absence_modal">
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
                            <a class="nav-link" data-bs-toggle="tab" href="#passee" role="tab" aria-selected="false"
                                tabindex="-1">
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
                                        @forelse([] as $employe)
                                            <tr>
                                                <td>
                                                    <span>{{ $employe->matricule }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->nom }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->prenom }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->gestionnaire?->nom ?? '---' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        {{-- Boutons avec composant --}}
                                                        @can('Voir Detail Employé')
                                                            <x-action-button type="outline-info" icon="fas fa-eye"
                                                                tooltip="Voir détails"
                                                                href="{{ route('rh-employe.show', $employe->id) }}" />
                                                        @endcan
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse([] as $employe)
                                            <tr>
                                                <td>
                                                    <span>{{ $employe->matricule }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->nom }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->prenom }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $employe->gestionnaire?->nom ?? '---' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        {{-- Boutons avec composant --}}
                                                        @can('Voir Detail Employé')
                                                            <x-action-button type="outline-info" icon="fas fa-eye"
                                                                tooltip="Voir détails"
                                                                href="{{ route('rh-employe.show', $employe->id) }}" />
                                                        @endcan
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
                    </div>
                </div>
                <hr class="">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <span class="badge bg-info">0 en attente</span>
                    <span class="badge bg-success">0 approuvée</span>
                </div>
            </x-table-card>
        </div>
        <div class="col-12 col-lg-3">
            <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
            </x-table-card>
        </div>
    </div>
    @if ($showAddAbsenceModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Nouvelle demande</h5>
                        <x-action-button type="close" wire-click="toogle_add_absence_modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        {{-- <livewire:rh::employe.employe-form /> --}}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
