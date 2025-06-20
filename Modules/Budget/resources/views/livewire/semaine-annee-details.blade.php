<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <!-- Filtres et recherche -->
    <x-table-card title="Filtres" icon="mdi mdi-filter me-2">
        <div>
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date début :</label>
                    <input type="date" class="form-control" wire:model="dateDebut">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date fin :</label>
                    <input type="date" class="form-control" wire:model="dateFin">
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="showOnlyActive" wire:model="showOnlyActive">
                        <label class="form-check-label" for="showOnlyActive">
                            Semaines actives seulement
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <x-action-button type="primary flex-fill" icon="mdi mdi-magnify me-1" wireClick="applyFilters"
                            size="md" loadingTarget="applyFilters" text="Filtrer" loading="true" />
                        <x-action-button type="outline-secondary flex-fill" icon="mdi mdi-refresh me-1"
                            wireClick="resetFilters" size="md" loadingTarget="resetFilters" text="Réinitialiser"
                            loading="true" />
                    </div>
                </div>
            </div>
        </div>
    </x-table-card>


    <!-- Tableau des feuilles de temps -->
    <x-table-card title="Semaines de l'années" icon="mdi mdi-file-table me-2">
        <div class="table-responsive">
            <table class="table table-nowrap align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Semaine</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th class="text-center">Actif</th>
                        <th class="text-center">Paie</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feuilles as $feuille)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                            <span class="fw-semibold text-primary">{{ $feuille->numero_semaine }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Semaine {{ $feuille->numero_semaine }}</h6>
                                        <p class="text-muted mb-0 small">{{ $feuille->periode }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $feuille->debut->format('d/m/Y') }}</td>
                            <td>{{ $feuille->fin->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($feuille->actif)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="mdi mdi-check-circle me-1"></i>Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="mdi mdi-close-circle me-1"></i>Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($feuille->est_semaine_de_paie)
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="mdi mdi-cash me-1"></i>Semaine de paie
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">
                                        <i class="mdi mdi-calendar me-1"></i>Normale
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <x-action-button type="light dropdown-toggle" icon="mdi mdi-dots-horizontal"
                                        size="sm" dataBsToogle='dropdown' />
                                    <ul class="dropdown-menu">
                                        @if ($feuille->actif)
                                            <li>
                                                <x-action-button type=" dropdown-item"
                                                    icon="mdi mdi-pause-circle text-warning me-2" size="md"
                                                    text="Désactiver"
                                                    wireClick="desactiverFeuille({{ $feuille->id }})" />
                                            </li>
                                        @else
                                            <li>
                                                <x-action-button type=" dropdown-item"
                                                    icon="mdi mdi-play-circle text-success me-2" size="md"
                                                    text="Activer" wireClick="activerFeuille({{ $feuille->id }})" />
                                            </li>
                                        @endif

                                        <li>
                                            <x-action-button type=" dropdown-item"
                                                icon="{{ $feuille->est_semaine_de_paie ? 'mdi mdi-cash-remove text-secondary me-2' : 'mdi mdi-cash-plus text-warning me-2' }}"
                                                size="md"
                                                text="{{ $feuille->est_semaine_de_paie ? 'Retirer semaine de paie' : 'Marquer semaine de paie' }}"
                                                wireClick="toggleSemaineDePaie({{ $feuille->id }})" />
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="mdi mdi-file-table text-muted mb-3" style="font-size: 48px;"></i>
                                    <h6 class="text-muted">Aucune semaine trouvée</h6>
                                    @if ($appliedDateDebut || $appliedDateFin || $appliedShowOnlyActive)
                                        <p class="text-muted small mb-3">Essayez de modifier vos filtres</p>
                                        <x-action-button type="outline-primary"
                                            icon="fas fa-refresh" size="sm"
                                            text="Réinitialiser les filtres" wireClick="resetFilters" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($feuilles->hasPages())
                <div class="mt-3">
                    {{ $feuilles->links() }}
                </div>
            @endif
        </div>
    </x-table-card>

    <!-- Statistiques rapides -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="mdi mdi-file-table" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $statistiques['total'] }}</h4>
                    <p class="text-muted mb-0 small">Total semaines</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="mdi mdi-check-circle" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $statistiques['actives'] }}</h4>
                    <p class="text-muted mb-0 small">Semaines actives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-close-circle" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $statistiques['inactives'] }}</h4>
                    <p class="text-muted mb-0 small">Semaines non actives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="mdi mdi-cash" style="font-size: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $statistiques['semaines_paie'] }}</h4>
                    <p class="text-muted mb-0 small">Semaines de paie</p>
                </div>
            </div>
        </div>
    </div>
</div>
