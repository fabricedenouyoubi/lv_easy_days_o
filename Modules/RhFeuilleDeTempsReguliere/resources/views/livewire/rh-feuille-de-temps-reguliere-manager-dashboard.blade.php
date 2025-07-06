<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <!-- Heures ce mois -->
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <div class="avatar-title rounded bg-primary bg-gradient">
                                    <i class="mdi mdi-clock-outline text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">
                                @if($this->isManager())
                                    Heures ce mois
                                @else
                                    Mes heures ce mois
                                @endif
                            </p>
                            <h4 class="mb-0">{{ number_format($stats['heures_ce_mois'] ?? 0, 1) }}h</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-end ms-2">
                            <div class="badge rounded-pill font-size-13 bg-success-subtle text-success">
                                <i class="mdi mdi-trending-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feuilles en attente -->
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <div class="avatar-title rounded bg-warning bg-gradient">
                                    <i class="mdi mdi-file-clock-outline text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">
                                @if($this->isManager())
                                    Feuilles à valider
                                @else
                                    Mes feuilles en attente
                                @endif
                            </p>
                            <h4 class="mb-0">{{ $stats['feuilles_en_attente'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feuilles validées -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <div class="avatar-title rounded bg-success bg-gradient">
                                    <i class="mdi mdi-checkmark-circle-outline text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">
                                @if($this->isManager())
                                    Validées cette semaine
                                @else
                                    Mes feuilles validées
                                @endif
                            </p>
                            <h4 class="mb-0">{{ $stats['feuilles_validees_semaine'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques absences -->
    <div class="row mb-4">
        <!-- Absences en attente -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <div class="avatar-title rounded bg-info bg-gradient">
                                    <i class="mdi mdi-calendar-clock text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">
                                @if($this->isManager())
                                    Absences à valider
                                @else
                                    Mes demandes en attente
                                @endif
                            </p>
                            <h4 class="mb-0">{{ $stats['absences_en_attente'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absences validées -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <div class="avatar-title rounded bg-success bg-gradient">
                                    <i class="mdi mdi-calendar-check text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">
                                @if($this->isManager())
                                    Validées ce mois
                                @else
                                    Mes absences validées
                                @endif
                            </p>
                            <h4 class="mb-0">{{ $stats['absences_validees_mois'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($this->isManager())
        <!-- Section gestionnaire : Onglets de validation -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'feuilles' ? 'active' : '' }}" 
                                wire:click="setActiveTab('feuilles')" type="button">
                            <i class="mdi mdi-clock-time-four-outline me-1"></i>
                            Feuilles de temps ({{ $stats['feuilles_en_attente'] }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'absences' ? 'active' : '' }}" 
                                wire:click="setActiveTab('absences')" type="button">
                            <i class="mdi mdi-calendar-clock me-1"></i>
                            Demandes d'absence ({{ $stats['absences_en_attente'] }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <!-- Le contenu des onglets reste le même -->
                <div class="tab-content">
                    {{-- Onglet Feuilles de temps --}}
                    @if($activeTab === 'feuilles')
                        <!-- Contenu existant des feuilles -->
                    @endif

                    {{-- Onglet Demandes d'absence --}}
                    @if($activeTab === 'absences')
                        <!-- Contenu existant des absences -->
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Section employé : Aperçu de mes données -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Mes feuilles de temps récentes</h5>
                <div class="text-center py-3">
                    <p class="text-muted">
                        <a href="{{ route('feuille-temps.list') }}" class="btn btn-primary">
                            <i class="mdi mdi-view-list me-1"></i>
                            Voir mes feuilles de temps
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>