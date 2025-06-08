<div>
    <!-- Messages de feedback -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- En-tête avec recherche -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-calendar-outline me-2"></i>
                        Historique des Années Financières
                    </h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Barre de recherche -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Rechercher par année..." 
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
                                    @if($annee->actif)
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
                                        <!-- Bouton Voir feuilles de temps -->
                                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center rounded-3 shadow-sm" 
                                                style="width: 38px; height: 38px; transition: all 0.2s ease;"
                                                wire:click="voirFeuillesDeTemps({{ $annee->id }})"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                title="Voir les feuilles de temps">
                                            <i class="mdi mdi-file-table fs-5"></i>
                                        </button>

                                        <!-- Bouton Clôturer (si active et avec permission) -->
                                        @if($annee->actif)
                                            <button class="btn btn-danger btn-sm rounded-3 px-3 d-inline-flex align-items-center" 
                                                    wire:click="confirmCloture({{ $annee->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="Clôturer l'année">
                                                <i class="mdi mdi-close-circle-outline me-1"></i>
                                                Clôturer
                                            </button>
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
    </div>

    <!-- Modal Confirmation Clôture -->
    @if($showClotureModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Clôturer une année financière existante</h5>
                        <button type="button" class="btn-close" wire:click="cancelCloture"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Êtes-vous sûr de vouloir clôturer cette année financière ?</strong>
                        </div>
                        <hr>
                        <p>Cette décision va déclencher les opérations suivantes :</p>
                        <ul>
                            <li>Verrouiller les feuilles de temps de l'année financière courante</li>
                            <li>Fermer l'année financière courante</li>
                            <li>Générer une nouvelle année financière</li>
                            <li>Générer de nouveaux jours fériés</li>
                            <li>Transférer les codes de travail dans la nouvelle année financière</li>
                            <li>Transférer les soldes de caisses de temps et vacances</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-warning" wire:click="cancelCloture">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-outline-danger" wire:click="cloturerEtCreerSuivante">
                            Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialiser les tooltips
    document.addEventListener('livewire:navigated', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush