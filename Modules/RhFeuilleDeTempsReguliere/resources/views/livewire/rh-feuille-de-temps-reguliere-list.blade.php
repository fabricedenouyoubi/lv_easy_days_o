<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <div class="row">
        <!-- Main content - 9 colonnes -->
        <div class="col-12 col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>
                                Feuilles de temps de {{ $employe->prenom }} {{ $employe->nom }}
                            </h5>
                        </div>
                        <div>
                            <span class="badge bg-info">{{ $anneeFinanciere->libelle ?? 'Année active' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Profil employé -->
                    <div class="p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                                <span class="font-size-24 text-primary fw-bold">
                                    {{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $employe->prenom }} {{ $employe->nom }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-clock-outline me-1"></i>
                                    Semaine normale : 35h 
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des feuilles de temps -->
                    <div class="table-responsive">
                        @if($feuilles_temps->count() > 0)
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>N° Semaine</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Statut</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feuilles_temps as $semaine)
                                        @php
                                            $statut = $this->getStatutFormate($semaine);
                                            $actions = $this->getActionsDisponibles($semaine);
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $semaine->numero_semaine }}</span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($semaine->debut)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($semaine->fin)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $statut['class'] }} px-3 py-2">
                                                    <i class="{{ $statut['icon'] }} me-1"></i>
                                                    {{ $statut['text'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    @foreach($actions as $action)
                                                        @if(isset($action['route']))
                                                            {{-- Bouton avec route --}}
                                                            <x-action-button 
                                                                type="{{ $action['type'] }}" 
                                                                size="sm"
                                                                icon="{{ $action['icon'] }}"
                                                                text="{{ $action['text'] }}"
                                                                href="{{ route($action['route'], $action['params']) }}"
                                                                tooltip="{{ $action['text'] }} la feuille de temps"
                                                            />
                                                        @else
                                                            {{-- Bouton avec action Livewire --}}
                                                            <x-action-button 
                                                                type="{{ $action['type'] }}" 
                                                                size="sm"
                                                                icon="{{ $action['icon'] }}"
                                                                text="{{ $action['text'] }}"
                                                                wireClick="{{ $action['action'] }}({{ implode(',', $action['params']) }})"
                                                                tooltip="{{ $action['text'] }} une feuille de temps"
                                                            />
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="mdi mdi-clipboard-outline text-muted" style="font-size: 48px;"></i>
                                </div>
                                <h5>Aucune feuille de temps active</h5>
                                <p class="text-muted">Il n'y a pas de feuilles de temps actives pour cette période.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pagination -->
                    @if($feuilles_temps->hasPages())
                        <div class="p-3 border-top">
                            {{ $feuilles_temps->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - 3 colonnes -->
        <div class="col-12 col-lg-3 mt-4 mt-lg-0">
            <!-- Informations générales -->
            <x-table-card title="Informations" icon="fas fa-business-time">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Semaine normale</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">35h</span>
                </div>
                
                <!-- Jours fériés et anniversaires à venir (exemple) -->
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">
                        <i class="fas fa-star text-warning me-1"></i>
                        Anniversaire
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        10 Avril 1997
                    </span>
                </div>
            </x-table-card>

            <!-- Banque de temps -->
            <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Vacances</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        {{ $banqueTemps['vacances'] }}h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Banque de temps</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        {{ $banqueTemps['banque_temps'] }}h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Heure CSN</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        {{ $banqueTemps['heure_csn'] }}h
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-3">
                    <span class="text-muted">Total des heures en banque</span>
                    <span class="badge bg-dark px-3 py-2 rounded-pill">
                        {{ $banqueTemps['total_heures_banque'] }}h
                    </span>
                </div>
            </x-table-card>
        </div>
    </div>
</div>
