<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    {{-- Layout principal --}}
    <div class="row">
        {{-- Colonne principale - Tableau --}}
        <div class="col-lg-8">
            <x-table-card title="Liste des employés" icon="fas fa-users" button-text="Nouveau employé"
                button-action="{{ auth()->user()->can('Ajouter Employé') ? 'showCreateModal' : '' }}">

                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <th>Gestionnaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employes as $employe)
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
                                            {{-- Bouton avec Details Employe --}}
                                            @can('Voir Detail Employé')
                                                <x-action-button type="outline-info" icon="fas fa-eye"
                                                    tooltip="Voir détails"
                                                    href="{{ route('rh-employe.show', $employe->id) }}" />
                                            @endcan

                                            @if (auth()->user()->hasRole('ADMIN') && auth()->user()->id != $employe->id)
                                                {{-- Bouton Ajouter de demande d'absence --}}
                                                <x-action-button type="outline-primary" icon="fas fa-clock"
                                                    tooltip="Ajouter une demande d'absence"
                                                    wireClick="open_add_employe_absence_modal({{ $employe->id }}, '{{ $employe->nom }} {{ $employe->prenom }}')" />
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun Employé trouvé</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $employes->links() }}
                </div>
            </x-table-card>
        </div>
        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card filterAction="getEmployes">
                {{-- Filtre par matricule --}}
                <div class="mb-3">
                    <label for="matricule_searched" class="form-label">Matricule</label>
                    <input type="text" id="matricule_searched" class="form-control"
                        placeholder="Rechercher par Matricule..." wire:model.defer="matricule_searched">
                </div>
                {{-- Filtre par nom --}}
                <div class="mb-3">
                    <label for="nom_searched" class="form-label">Nom</label>
                    <input type="text" id="nom_searched" class="form-control" placeholder="Rechercher par Nom..."
                        wire:model.defer="nom_searched">
                </div>
                {{-- Filtre par prenom --}}
                <div class="mb-3">
                    <label for="prenom_searched" class="form-label">Prénom</label>
                    <input type="text" id="prenom_searched" class="form-control"
                        placeholder="Rechercher par Prénom..." wire:model.defer="prenom_searched">
                </div>
                {{-- Filtre par gestionnaire --}}
                <div class="mb-3">
                    <label for="gestionnaire_searched" class="form-label">Gestionnaire</label>
                    <input type="text" id="gestionnaire_searched" class="form-control"
                        placeholder="Rechercher par Gestionnaire..." wire:model.defer="gestionnaire_searched">
                </div>
            </x-filter-card>
        </div>
    </div>

    {{-- Formulaire d'ajout d'un employe --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-users"></i> Nouveau employé</h5>
                        <x-action-button type="close" wire-click="closeModal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rh::employe.employe-form />
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
                        <h5 class="modal-title"><i class="fas fa-clock"></i> Nouvelle demande pour
                            <strong>{{ $employeNom }}</strong>
                        </h5>
                        <x-action-button type="close" wire-click="close_add_employe_absence_modal"
                            aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-form :employeId="$employeId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
