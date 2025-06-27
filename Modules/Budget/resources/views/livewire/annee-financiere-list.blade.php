<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <x-table-card title="Historique des Années Financières" icon="mdi mdi-calendar-outline me-2"
        button-text="Nouvelle Année"
        button-action="{{ auth()->user()->can('Ajouter une année financière') ? 'showCreateModal' : '' }}">

        <div>
            <!-- Barre de recherche -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Rechercher par année..."
                                wire:model.live.debounce.300ms="search">
                            <i class="mdi mdi-magnify position-absolute top-50 end-0 translate-middle-y me-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des années financières -->
            <div class="table-responsive">
                <table class="table table-nowrap align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Statut</th>
                            <th class="text-center">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($annees as $annee)
                            <tr>
                                <td>{{ $annee->debut->format('d/m/Y') }}</td>
                                <td>{{ $annee->fin->format('d/m/Y') }}</td>
                                <td>
                                    @if ($annee->actif)
                                        <span class="badge bg-success d-inline-flex align-items-center px-3 py-2">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            Actif
                                        </span>
                                    @else
                                        <span class="badge bg-danger d-inline-flex align-items-center px-3 py-2">
                                            <i class="mdi mdi-close-circle me-1"></i>
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Bouton Voir feuilles de temps avec génération automatique -->
                                        @can('Générer les semaines d\'une année')
                                            <x-action-button type="outline-primary" icon="mdi mdi-file-table" size="sm"
                                                tooltip="Voir les feuilles de temps"
                                                wireClick="voirFeuillesDeTemps({{ $annee->id }})"
                                                loadingTarget="voirFeuillesDeTemps({{ $annee->id }})" />
                                        @endcan

                                        <!-- Bouton Activer - si pas active -->
                                        @can('Activer une année financière')
                                            @if ($annee->statut !== 'ACTIF')
                                                <x-action-button type="outline-success" icon="mdi mdi-check-circle"
                                                    size="sm" tooltip="Activer"
                                                    wireClick="activer({{ $annee->id }})" />
                                            @endif
                                        @endcan

                                        <!-- Bouton Clôturer -->
                                        @if ($annee->actif)
                                            <!-- <button class="btn btn-outline-warning btn-sm rounded-3 px-3 d-inline-flex align-items-center"
                                                    onclick="alert('Pas encore disponible')"
                                                    data-bs-toggle="tooltip"
                                                    title="Clôturer l'année">
                                                <i class="mdi mdi-close-circle-outline me-1"></i>
                                                Clôturer
                                            </button> -->
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="mdi mdi-calendar-outline text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2 mb-0">Aucune année financière disponible.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $annees->links() }}
            </div>
        </div>
    </x-table-card>

    <!-- Notification de génération en cours -->
    <div wire:loading wire:target="voirFeuillesDeTemps" class="position-fixed top-0 start-50 translate-middle-x mt-3"
        style="z-index: 1060;">
        <div class="alert alert-info d-flex align-items-center shadow-lg" role="alert">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <div>
                <strong>En cours de traitement...</strong><br>
                <!--<small>Création des semaines, et jours fériés pour cette année financière.</small>-->
            </div>
        </div>
    </div>

    <!-- Modal Formulaire -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="mdi mdi-calendar-outline me-2"></i>
                            {{ $editingId ? 'Modifier' : 'Créer' }} une Année Financière
                        </h5>
                        <x-action-button type="close" wireClick="closeModal" />
                    </div>
                    <div class="modal-body">
                        <livewire:budget::annee-financiere-form :anneeId="$editingId" :key="$editingId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@push('scripts')
    <script>
        // Initialiser les tooltips
        document.addEventListener('livewire:navigated', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
