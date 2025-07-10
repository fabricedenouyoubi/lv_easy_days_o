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
                            <span class="badge bg-info">
                                @if($anneeFinanciere)
                                {{ \Carbon\Carbon::parse($anneeFinanciere->debut)->locale('fr')->translatedFormat('F Y') }} -
                                {{ \Carbon\Carbon::parse($anneeFinanciere->fin)->locale('fr')->translatedFormat('F Y') }}
                                @else
                                Année active
                                @endif
                            </span>
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
                                                tooltip="{{ $action['text'] }} la feuille de temps" />
                                            @else
                                            {{-- Bouton avec action Livewire --}}
                                            <x-action-button
                                                type="{{ $action['type'] }}"
                                                size="sm"
                                                icon="{{ $action['icon'] }}"
                                                text="{{ $action['text'] }}"
                                                wireClick="{{ $action['action'] }}({{ implode(',', $action['params']) }})"
                                                tooltip="{{ $action['text'] }} une feuille de temps" />
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
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $informationsEmploye['semaine_normale'] }}</span>
                </div>

                @if($informationsEmploye['prochain_jour_ferie'])
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        <i class="fas fa-calendar-times text-info me-1"></i>
                        Prochain jour férié
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold">
                        {{ \Carbon\Carbon::parse($informationsEmploye['prochain_jour_ferie']->date)->format('d M Y') }}
                    </span>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">
                        <i class="fas fa-birthday-cake text-warning me-1"></i>
                        Anniversaire
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold">
                        {{ \Carbon\Carbon::parse($informationsEmploye['anniversaire'])->format('d M Y') }}
                    </span>
                </div>
            </x-table-card>

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
        </div>
    </div>
</div>